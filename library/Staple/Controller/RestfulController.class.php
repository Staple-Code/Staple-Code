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

use \Exception;
use Staple\Auth;
use Staple\Config;
use Staple\Dev;
use Staple\Error;
use Staple\Exception\AuthException;
use Staple\Exception\PageNotFoundException;
use Staple\Exception\RoutingException;
use Staple\Json;
use Staple\Link;
use Staple\Main;
use Staple\Route;
use Staple\View;

abstract class RestfulController
{
	use \Staple\Traits\Helpers;

	const DEFAULT_ACTION = 'index';
	
	protected $openMethods = array();
	protected $accessLevels = array();
	protected $open = false;
	
	/**
	 * 
	 * Controller constructor creates an instance of View and saves it in the $view
	 * property. It then calls the overridable method _start() for additional boot time
	 * procedures.
	 */
	public function __construct()
	{
		//Set default access levels
		$methods = get_class_methods(get_class($this));
		foreach($methods as $acMethod)
		{
			if(substr($acMethod, 0,1) != '_')
			{
				$this->accessLevels[$acMethod] = 1;
			}
		}
	}
	
	/**
	 * Overridable and empty method allows for additional boot time procedures.
	 * This method is called by the front controller.
	 */
	public function _start() {}
	
	/*----------------------------------------Auth Functions----------------------------------------*/
	
	/**
	 * Returns a boolean true or false whether the method requires authentication
	 * before being dispatched from the front controller.
	 * @param string $method
	 * @return bool
	 * @throws Exception
	 */
	public function _auth($method)
	{
		$method = (string)$method;
		if(!ctype_alnum($method))
		{
			throw new Exception('Authentication Validation Error', Error::AUTH_ERROR);
		}
		else
		{
			if(method_exists($this,$method))
			{
				//Is the controller completely open?
				if($this->open === true)
				{
					return true;
				}
				elseif(array_search($method, $this->openMethods) !== FALSE)		//Is the requested method open?
				{
					return true;
				}
				elseif(Auth::get()->isAuthed() && Auth::get()->getAuthLevel() >= $this->_authLevel($method))	//Does the authed user have the required access level?
				{
					return true;
				}
			}
			else
			{
				throw new Exception('Authentication Validation Error', Error::AUTH_ERROR);
			}
		}
		return false;
	}
	
	/**
	 * 
	 * Returns the access level required for this method.
	 * @param string | array $method
	 * @throws Exception
	 * @return int
	 */
	public function _authLevel($method)
	{
		$method = (string)$method;
		if(!ctype_alnum($method))
		{
			throw new Exception('Authentication Validation Error: Invalid Method', Error::AUTH_ERROR);
		}
		else
		{
			if(method_exists($this,$method))
			{
				if(array_key_exists($method, $this->accessLevels) === true)
				{
					return (int)$this->accessLevels[$method];
				}
				else
				{
					//return default auth level if non assigned.
					return 1;
					//throw new Exception('Authentication Validation Error: No Auth Level', Error::AUTH_ERROR);
				}
			}
			else
			{
				throw new Exception('Authentication Validation Error: Method Not Found', Error::AUTH_ERROR);
			}
		}
	}
	
	/**
	 * 
	 * Replaces the default permission level with the specified permission level. All method
	 * specific access levels will be overwritten. This should be called in controller startup.
	 * @param int $level
	 */
	protected function _requiredControllerAccessLevel($level)
	{
		$methods = get_class_methods(get_class($this));
		foreach($methods as $acMethod)
		{
			if(substr($acMethod, 0,1) != '_')
			{
				$this->accessLevels[$acMethod] = (int)$level;
			}
		}
		$this->openMethods = array();
	}
	
	/**
	 * Specifies the required access level for the specified action. This should be called
	 * in the Controller startup.
	 * @param string $for
	 * @param int $level
	 * @throws Exception
	 */
	protected function _requiredActionAccessLevel($for,$level)
	{
		$level = (int)$level;
		$for = (string)$for;
		if(!ctype_alnum($for))
		{
			throw new Exception('Cannot change method permissions.', Error::AUTH_ERROR);
		}
		else
		{
			if(method_exists($this, $for))
			{
				if($level < 0)
				{
					throw new Exception('Cannot change method permissions.', Error::AUTH_ERROR);
				}
				else
				{
					if($level == 0)
					{
						$this->_openMethod($for);
						if(array_key_exists($for, $this->accessLevels))
						{
							unset($this->accessLevels[$for]);
						}
					}
					else
					{
						$this->accessLevels[$for] = $level;
					}
				}
			}
			else 
			{
				throw new Exception('Cannot change method permissions on a non-existant method.', Error::AUTH_ERROR);
			}
		}
	}
	
	/**
	 * Sent a string it allows one method to be accessed without authentication. When sent
	 * an array, it allows all the values method names without authentication.
	 * @param string | array $method
	 * @throws Exception
	 * @return bool
	 */
	protected function _openMethod($method)
	{
		if(is_array($method))
		{
			foreach($method as $mName)
			{
				if(!ctype_alnum($mName))
				{
					throw new Exception('Cannot change method permissions.', Error::AUTH_ERROR);
				}
				else
				{
					if(array_search($mName, $this->openMethods) === false)
					{
						$this->openMethods[] = $mName;
						$this->accessLevels[$mName] = 0;
					}
				}
			}
			return true;
		}
		else
		{
			if(!ctype_alnum($method))
			{
				throw new Exception('Cannot change method permissions.', Error::AUTH_ERROR);
			}
			else
			{
				if(array_search($method, $this->openMethods) === false)
				{
					$this->openMethods[] = $method;
					$this->accessLevels[$method] = 0;
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * This function opens the entire controller to be accessed without authentication.
	 */
	protected function _openAll()
	{
		$methods = get_class_methods(get_class($this));
		foreach($methods as $acMethod)
		{
			if(substr($acMethod, 0,1) != '_')
			{
				$this->accessLevels[$acMethod] = 0;
			}
		}
		$this->open = true;
	}

	/*----------------------------------------Routing Functions----------------------------------------*/

	/**
	 * Route Restful provider.
	 * @param array $route
	 * @return bool
	 * @throws AuthException
	 * @throws PageNotFoundException
	 * @throws RoutingException
	 */
	public function _route(array $route = [])
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

		$requestMethod = $_SERVER['REQUEST_METHOD'];
		if(ctype_alpha($requestMethod))
		{
			$method = strtolower($requestMethod) . $action;

			//Check for the action existence
			if(method_exists($this, $method))
			{
				//Run the startup method
				$this->_start();

				//Check if global Auth is enabled.
				if(Config::getValue('auth', 'enabled') != 0)
				{
					//Check the sub-controller for access to the method
					if($this->_auth($method) === true)
					{
						//Everything went well, dispatch the controller.
						$this->_dispatch($method, $params);
					}
					else
					{
						//No Authentication
						throw new AuthException('Not Authorized', Error::AUTH_ERROR);
					}
				}
				else
				{
					//No authentication needed, dispatch the controller
					$this->_dispatch($method, $params);
				}

				//Return true so that we don't hit the exception.
				return true;
			}
		}

		//If a valid page cannot be found, throw page not found exception
		throw new PageNotFoundException('Page Not Found',Error::PAGE_NOT_FOUND);
	}

	/**
	 * Function executes a controller action passing parameters using call_user_func_array().
	 * It also builds the view for the route.
	 *
	 * @param string $method
	 * @param array $params
	 * @throws RoutingException
	 */
	protected function _dispatch(string $method, array $params)
	{
		//Call the controller action
		$actionMethod = new \ReflectionMethod($this,$method);
		$return = $actionMethod->invokeArgs($this, $params);

		if($return instanceof View)		//Check for a returned View object
		{
			//If the view does not have a controller name set, set it to the currently executing controller.
			if(!$return->hasController())
			{
				$loader = Main::get()->getLoader();
				$conString = get_class($this);

				$return->setController(substr($conString,0,strlen($conString)-strlen($loader::PROVIDER_SUFFIX)));
			}

			//If the view doesn't have a view set, use the route's action.
			if(!$return->hasView())
			{
				$return->setView($method);
			}

			$return->build();
		}
		elseif ($return instanceof Json)	//Check for a Json object to be converted and echoed.
		{
			echo json_encode($return);
		}
		elseif ($return instanceof Route)	//Allow a controller to return a route to redirect the program execution to.
		{
			Main::get()->run($return);
		}
		elseif ($return instanceof Link)	//Redirect to a link location.
		{
			header('Location: '.$return);
		}
		elseif (is_object($return))		//Check for another object type
		{
			//If the object is stringable, covert it to a string and output it.
			$class = new \ReflectionClass($return);
			if ($class->implementsInterface('JsonSerializable'))
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
		elseif(is_string($return))		//If the return value was simply a string, echo it out.
		{
			echo $return;
		}
		else
		{
			return;
		}
	}
}