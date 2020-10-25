<?php
/**
 * The RestfulController object handles RESTful endpoints.
 * 
 * @author Ironpilot
 * @copyright Copyright (c) 2011, STAPLE CODE
 * 
 * This file is part of the STAPLE Framework.
 * 
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by the 
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 * 
 * The STAPLE Framework is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for 
 * more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Staple\Controller;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Staple\Auth\Auth;
use Staple\Auth\IAuthService;
use Staple\Autoload;
use Staple\Dev;
use Staple\Error;
use Staple\Exception\AuthException;
use Staple\Exception\NotAuthorizedException;
use Staple\Exception\PageNotFoundException;
use Staple\Exception\RoutingException;
use Staple\Json;
use Staple\Link;
use Staple\Main;
use Staple\Request;
use Staple\Response;
use Staple\Route;
use Staple\Traits\Helpers;
use Staple\View;

abstract class RestfulController
{
	use Helpers;

	const DEFAULT_ACTION = 'index';

	/** @var IAuthService $_authService */
	private $_authService;

	private $_headerCorsAllowedMethodsSent = false;
	private $_headerCorsAllowedOriginSent = false;
	private $_headerCorsAllowHeadersSent = false;

	/**
	 * Default Constructor
	 * @var IAuthService $authService
	 */
	public function __construct(IAuthService $authService)
	{
		$this->_authService = $authService;
	}
	
	/**
	 * Overridable and empty method allows for additional boot time procedures.
	 * This method is called every time an action is executed and before authentication
	 * of the route occurs.
	 */
	public function _start() {}

	/**
	 * Overridable and empty method allows for additional actions to happen before each routed action.
	 */
	protected function before() {}

	/**
	 * Overridable and empty method allows for additional actions to happen after each routed action.
	 */
	protected function after() {}
	
	/*----------------------------------------Auth Functions----------------------------------------*/
	
	/**
	 * Returns a boolean true or false whether the method requires authentication
	 * before being dispatched.
	 * @param string $method
	 * @return bool
	 * @throws Exception
	 */
	protected function auth(string $method)
	{
		if(!ctype_alnum($method))		//Non-routable method check
		{
			throw new Exception('Authentication Validation Error', Error::AUTH_ERROR);
		}
		else
		{
			if(method_exists($this,$method))
			{
				$auth = Auth::get();
				$reflectMethod = new ReflectionMethod($this, $method);
				$reflectClass = new ReflectionClass($this);
				$classComments = $reflectClass->getDocComment();
				$methodComments = $reflectMethod->getDocComment();

				//Auth Level
				$routeAuth = true;
				if(stripos($classComments, Auth::AUTH_FLAG_LEVEL) !== false)
				{
					$levelSplit = explode(Auth::AUTH_FLAG_LEVEL, $classComments);
					$eolSplit = explode($levelSplit[1], '\n');
					$authLevel = trim($eolSplit[0]);
					$routeAuth = $auth->authRoute(Route::create([str_ireplace(Autoload::CONTROLLER_SUFFIX, '', $reflectClass->getName()), $method]), $authLevel, $reflectClass, $reflectMethod);

				}
				elseif(stripos($methodComments, Auth::AUTH_FLAG_LEVEL) !== false)
				{
					$levelSplit = explode(Auth::AUTH_FLAG_LEVEL, $methodComments);
					$eolSplit = explode($levelSplit[1], '\n');
					$authLevel = trim($eolSplit[0]);
					$routeAuth = $auth->authRoute(Route::create([str_ireplace(Autoload::CONTROLLER_SUFFIX, '', $reflectClass->getName()), $method]), $authLevel, $reflectClass, $reflectMethod);
				}

				//Auth Protection
				return $auth->isAuthed() && $routeAuth;
			}
			else
			{
				throw new AuthException('Authentication Validation Error', Error::AUTH_ERROR);
			}
		}
	}

	/**
	 * Check for auth flags to see if we need to authenticate the method before routing.
	 * @param string $method
	 * @return bool
	 * @throws Exception
	 */
	protected function isAuthRequired(string $method)
	{
		if(!ctype_alnum($method))		//Non-routable method check
		{
			throw new Exception('Authentication Validation Error', Error::AUTH_ERROR);
		}
		else
		{
			if(method_exists($this, $method))
			{
				$reflectMethod = new ReflectionMethod($this, $method);
				$reflectClass = new ReflectionClass($this);
				$classComments = $reflectClass->getDocComment();
				$methodComments = $reflectMethod->getDocComment();

				//Auth Protection
				if(stripos($classComments, Auth::AUTH_FLAG_PROTECTED) !== false)            //The entire Controller is protected.
				{
					if(stripos($methodComments, Auth::AUTH_FLAG_OPEN) !== false)                    //Provider is protected but the method is open.
						return false;
					return true;
				}
				elseif(stripos($methodComments, Auth::AUTH_FLAG_PROTECTED) !== false)    //The method is protected.
				{
					return true;
				}
			}
		}
		return false;
	}


	/*----------------------------------------Routing Functions----------------------------------------*/

	/**
	 * Route Restful provider.
	 * @param array $route
	 * @return bool
	 * @throws AuthException
	 * @throws PageNotFoundException
	 * @throws RoutingException
	 * @throws Exception
	 */
	public function route(array $route = [])
	{
		//Set the Action
		if(count($route) >= 1)
		{
			$action = array_shift($route);
			if(ctype_alnum(str_replace('-', '', $action)) && ctype_alpha(substr($action, 0, 1)))
			{
				$action = ucfirst(Link::methodCase($action));
			}
			else
			{
				//Bad info in the route, error out.
				throw new RoutingException('Invalid Route', Error::PAGE_NOT_FOUND);
			}
		}
		else
		{
			$action = ucfirst(self::DEFAULT_ACTION);
		}

		//Set Parameters
		$params = $route;

		$requestMethod = Request::get()->getMethod();
		if(ctype_alpha($requestMethod))
		{
			$method = strtolower($requestMethod) . $action;

			//Check for the action existence
			if(method_exists($this, $method))
			{
				//Run the startup method
				$this->_start();

				//Check if we need to run auth for the requested method
				if($this->isAuthRequired($method))
				{
					if($this->_authService->doAuth(Request::BodyContent()))
					{
						//check for auth on the requested method.
						if($this->auth($method) === true)
						{
							//Everything went well, dispatch the provider action.
							$this->before();
							$this->dispatch($method, $params);
							$this->after();
							return true;
						}
					}

					//No Authentication
					throw new AuthException('Not Authorized', Error::AUTH_ERROR);
				}
				else
				{
					//dispatch the provider action.
					$this->before();
					$this->dispatch($method, $params);
					$this->after();
				}

				//Return true so that we don't hit the exception.
				return true;
			}
			elseif(Request::METHOD_OPTIONS == strtoupper($requestMethod))
			{
				// If a POST method exists with the same name as the OPTIONS request then we are
				// probably in a Pre-flight scenario
				if(method_exists($this, strtolower(Request::METHOD_POST).$action))
				{
					//If we have already sent the allowed origins header then we can return true.
					if($this->_headerCorsAllowedOriginSent === true)
						return true;
					else
						throw new RoutingException('Matching method for POST request found. Did you forget CORS headers or an options endpoint?');
				}
			}
		}

		//If a valid page cannot be found, throw page not found exception
		throw new PageNotFoundException();
	}

	/**
	 * Function executes a provider action passing parameters using call_user_func_array().
	 * It also builds the view for the route.
	 *
	 * @param string $method
	 * @param array $params
	 */
	protected function dispatch(string $method, array $params)
	{
		try
		{
			//Call the action
			$actionMethod = new ReflectionMethod($this, $method);
			$return = $actionMethod->invokeArgs($this, $params);

			if($return instanceof View)        //Check for a returned View object
			{
				//If the view does not have a controller name set, set it to the currently executing controller.
				if(!$return->hasController())
				{
					$loader = Main::get()->getLoader();
					$conString = get_class($this);

					$return->setController(substr($conString, 0, strlen($conString) - strlen($loader::PROVIDER_SUFFIX)));
				}

				//If the view doesn't have a view set, use the route's action.
				if(!$return->hasView())
				{
					$return->setView($method);
				}

				$return->build();
			}
			elseif($return instanceof Json)    //Check for a Json object to be converted and echoed.
			{
				echo json_encode($return);
			}
			elseif($return instanceof Route)    //Allow a provider to return a route to redirect the program execution to.
			{
				Main::get()->run($return);
			}
			elseif($return instanceof Link)    //Redirect to a link location.
			{
				header('Location: ' . $return);
			}
			elseif(is_object($return))        //Check for another object type
			{
				//If the object is stringable, covert it to a string and output it.
				$class = new ReflectionClass($return);
				if($class->implementsInterface('JsonSerializable'))
				{
					echo json_encode($return);
				}
				//If the object is stringable, covert to a string and output it.
				elseif((!is_array($return)) &&
					((!is_object($return) && settype($return, 'string') !== false) ||
						(is_object($return) && method_exists($return, '__toString'))))
				{
					echo (string)$return;
				}
				//If nothing else works, echo the object through the dump method.
				else
				{
					Dev::dump($return);
				}
			}
			elseif(is_string($return))        //If the return value was simply a string, echo it out.
			{
				echo $return;
			}
		}
		catch(AuthException $e)
		{
			$details = ($this->isInDevMode()) ? $e->getTraceAsString() : null;
			echo Json::authError($e->getMessage(), null, $details);
		}
		catch(NotAuthorizedException $e)
		{
			$details = ($this->isInDevMode()) ? $e->getTraceAsString() : null;
			echo Json::error($e->getMessage(), $e->getCode(), $details);
		}
		catch(Exception $e)
		{
			$details = ($this->isInDevMode()) ? $e->getTraceAsString() : null;
			echo Json::error($e->getMessage(), Json::DEFAULT_ERROR_CODE, $details);
		}
	}

	/*-----------------------------------HELPERS-----------------------------------*/

	protected function forceSecure()
	{
		if(!Request::isSecure())
		{
			echo Json::error(
				'Request must be secure.',
				400,
				'HTTPS is required for this request.'
			);
			exit();
		}
	}

	protected function addHeader($header, $value, $replace = true)
	{
		header($header.': '.$value, $replace);
	}

	protected function addAccessControlMethods(array $methods)
	{
		if(count($methods) == 0)
		{
			$methods = [
				Request::METHOD_GET,
				Request::METHOD_POST,
				Request::METHOD_PUT,
				Request::METHOD_PATCH,
				Request::METHOD_DELETE,
				Request::METHOD_OPTIONS
			];
		}
		header(Response::HEADER_ACCESS_CONTROL_ALLOW_METHODS.': '.implode(',', $methods));
		$this->_headerCorsAllowedMethodsSent = true;
	}

	protected function addAccessControlOrigin($origin = '*')
	{
		header(Response::HEADER_ACCESS_CONTROL_ALLOW_ORIGIN.': '.$origin);
		$this->_headerCorsAllowedOriginSent = true;
	}

	protected function addAccessControlAllowedHeaders(array $headers)
	{
		header(Response::HEADER_ACCESS_CONTROL_ALLOW_HEADERS.': '.implode(',',$headers));
		$this->_headerCorsAllowHeadersSent = true;
	}

	protected function addCorsHeaders($origin = '*', array $methods = [], array $headers = [])
	{
		$this->addAccessControlOrigin($origin);
		$this->addAccessControlMethods($methods);
		$this->addAccessControlAllowedHeaders($headers);
	}
}