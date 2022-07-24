<?php

/** 
 * This class will be a container for routes generated from link strings.
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
namespace Staple;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Staple\Auth\Auth;
use Staple\Controller\Controller;
use Staple\Controller\RestfulController;
use Staple\Exception\AuthException;
use Staple\Exception\ConfigurationException;
use Staple\Exception\PageNotFoundException;
use Staple\Exception\RoutingException;
use Exception;

class Route
{
	const ROUTE_MVC = 1;
	/** @deprecated  */
	const ROUTE_SCRIPT = 2;
	const ROUTE_FUNCTIONAL = 3;
	const CONTROLLER_SUFFIX = "Controller";
	const PROVIDER_SUFFIX = "Provider";
	const DEFAULT_ACTION = 'index';
	const DEFAULT_CONTROLLER = 'index';
	const ACCEPTABLE_ROUTE_SPECIAL_CHARACTERS = ['-','_'];
	const ACCEPTABLE_FUNCTION_ROUTE_CHARACTERS = ['{','}','_','-','/'];

	/**
	 * The name of the controller being executed.
	 * @var string
	 */
	protected $controller;
	/**
	 * Name of the action being executed.
	 * @var string
	 */
	protected $action;
	/**
	 * The parameters that are being sent to the action
	 * @var array[mixed]
	 */
	protected $params = array();
	/**
	 * Type of route: MVC route or script route.
	 * @var int
	 */
	protected $type;
	/**
	 * The string interpretation of the route.
	 * @var string
	 */
	protected $routeString;
	/**
	 * A callback method used for functional routing
	 * @var callable
	 */
	private $callbackFunction;
	/**
	 * Boolean to denote that the route is protected by the auth system.
	 * @var bool
	 */
	private $protected;
	/**
	 * Any additional route options. Used with Functional Routing
	 * @var array
	 */
	private $options;
	/**
	 * Static array of registered Functional Routes.
	 * @var [Route]
	 */
	private static $functionalRoutes = [];
	/**
	 * Callback functions to be executed before a functional route is executed.
	 * @var [callable]
	 */
	private static $beforeRouteCallbacks = [];
	/**
	 * Callback functions to be executed after a functional route is executed.
	 * @var [callable]
	 */
	private static $afterRouteCallbacks = [];

	/**
	 * Route constructor.
	 * @param mixed $route
	 * @throws RoutingException
	 * @throws ConfigurationException
	 */
	public function __construct($route = NULL)
	{
		//Check for sub-path configuration.
		$publicLocation = Config::getValue('application', 'public_location');
		if(strlen($publicLocation) && substr($route, 0, strlen($publicLocation)) === $publicLocation)
		{
			$route = substr($route, strlen($publicLocation));
		}

		//Check for functional Route
		if($this->matchesFunctionalRoute($route))
		{
			$this->setType(self::ROUTE_FUNCTIONAL);
		}

		if(is_array($route))
		{
			$this->processArrayRoute($route);
		}
		else
		{
			$this->processStringRoute($route);
		}
	}
	
	/**
	 * Returns the route as a link.
	 */
	public function __toString()
	{
		//Website Base - don't remember why this was here
		//$link = Staple_Config::getValue('application', 'public_location');
		
		//Add Controller
		$link = Link::urlCase($this->getController()).'/';
		
		//Add Action
		$link .= Link::urlCase($this->getAction());
		
		//Add Parameters
		if(count($this->params) >= 1)
		{
			$link .= '/'.implode('/', $this->params);
		}
		
		return $link;
	}

	/**
	 * Create and return an instance of the object.
	 * @param string $link
	 * @return static
	 * @throws RoutingException
	 * @deprecated
	 */
	public static function make($link = NULL)
	{
		return new static($link);
	}

	/**
	 * Create and return an instance of the object.
	 * @param string $link
	 * @return static
	 * @throws RoutingException
	 */
	public static function create($link = NULL)
	{
		return new static($link);
	}

	/**
	 * Execute the route
	 * @return bool
	 * @throws RoutingException
	 * @throws PageNotFoundException
	 * @throws AuthException
	 * @throws ReflectionException
	 */
	public function execute()
	{
		//Route Controller and actions
		$class = $this->getController();
		$method = $this->getAction();
		
		//The class name for the controller
		$dispatchClass = $class.self::CONTROLLER_SUFFIX;

		try
		{
			//Check for functional route matches
			if($this->getType() == self::ROUTE_FUNCTIONAL)
			{
				$route = $this->getFunctionalRouteObject($this);
				if($route->isProtected() === true)
				{
					$this->functionalRouteAuth($route);
				}

				$this->beforeFunctionalRouting();
				$this->dispatchFunctionalRoute($route);
				$this->afterFunctionalRouting();

				return true;
			}
			else
			{
				//Check for the controller existence
				if(class_exists($dispatchClass))
				{
					if(get_parent_class($dispatchClass) == RestfulController::class)
					{
						$this->routeToProvider($dispatchClass);
					}
					else
					{
						//Check for the action existence
						if(method_exists($dispatchClass, $method))
						{
							//If the controller has not been created yet, create an instance and store it in the front controller
							if(($controller = Main::controller($class)) == NULL)
							{
								/**
								 * @var Controller $controller
								 */
								$controller = new $dispatchClass();
								$controller->_start();

								//Store the controller object
								Main::controller($controller);
							}
							else
							{
								//If the controller already exists in the session just execute the start method again.
								$controller->_start();
							}

							//Verify that an instance of the controller class exists and is of the right type
							if($controller instanceof Controller)
							{
								//Check the sub-controller for access to the method
								if($controller->_auth($method) === true)
								{
									//Everything went well, dispatch the controller.
									$this->dispatchController();
								}
								else
								{
									//No Authentication, send us to the login screen.
									Auth::get()->noAuth($this);
								}

								//Return true so that we don't hit the exception.
								return true;
							}
						}
						else
						{
							throw new PageNotFoundException();
						}
					}
				}
			}
		}
		catch(PageNotFoundException $e)
		{
			//The class name for the controller
			$dispatchProvider = $class.self::PROVIDER_SUFFIX;

			if(class_exists($dispatchProvider))
			{
				if(get_parent_class($dispatchProvider) == RestfulController::class)
				{
					$this->routeToProvider($dispatchProvider);
					return true;
				}
			}
		}
		
		//If a valid page cannot be found, throw page not found exception
		throw new PageNotFoundException();
	}
	
	/**
	 * Function executes a controller action passing parameters using call_user_func_array().
	 * It also builds the view for the route.
	 *
	 * @throws RoutingException
	 * @throws ReflectionException
	 * @throws Exception
	 */
	protected function dispatchController()
	{
		$controller = Main::controller($this->getController());
		
		if($controller instanceof Controller)
		{
			//Call the controller action
			try
			{
				$actionMethod = new ReflectionMethod($controller, $this->getAction());
			}
			catch(ReflectionException $e)
			{
				throw new RoutingException('Failed Controller Method Reflection', $e->getCode(), $e);
			}
			$return = $actionMethod->invokeArgs($controller, $this->getParams());

			if($return instanceof View)		//Check for a returned View object
			{
				//If the view does not have a controller name set, set it to the currently executing controller.
				if(!$return->hasController())
				{
					$loader = Main::get()->getLoader();
					$conString = get_class($controller);

					$return->setController(substr($conString,0,strlen($conString)-strlen($loader::CONTROLLER_SUFFIX)));
				}

				//If the view doesn't have a view set, use the route's action.
				if(!$return->hasView())
				{
					$return->setView($this->getAction());
				}

				//Check for a controller layout and build it.
				if($controller->layout instanceof Layout)
				{
					$controller->layout->build(NULL,$return);
				}
				else
				{
					$return->build();
				}
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
				try
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
				catch (ReflectionException $e)
				{
					throw new RoutingException('Failed Controller Class Reflection', $e->getCode(), $e);
				}
			}
			elseif(is_string($return))		//If the return value was simply a string, echo it out.
			{
				echo $return;
			}
		}
		else
		{
			throw new RoutingException('Tried to dispatch to an invalid controller.', Error::APPLICATION_ERROR);
		}
	}

	/**
	 * Push the route to a Restful Controller
	 * @param $providerClass
	 * @throws RoutingException
	 * @throws Exception
	 */
	private function routeToProvider($providerClass)
	{
		$providerObject = new $providerClass(Auth::get());
		if($providerObject instanceof RestfulController)
		{
			$route = array_merge([$this->getAction()], $this->getParams());
			$providerObject->route($route);
		}
		else
		{
			unset($providerObject);
			throw new RoutingException('Invalid Routing Object');
		}
	}
	
	/**
	 * Redirect to the route location.
	 * @throws ConfigurationException
	 */
	public function redirect()
	{
		$base = Config::getValue('application', 'public_location');
		header('Location: '.$base.$this);
		exit(0);
	}
	
	/**
	 * @return string $controller
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * @return string $action
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @return array $params
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return int $type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set the type of the route
	 * @param int $type
	 * @return $this
	 */
	public function setType($type)
	{
		switch($type)
		{
			case self::ROUTE_MVC:
			case self::ROUTE_SCRIPT:
			case self::ROUTE_FUNCTIONAL:
				$this->type = (int)$type;
				break;
		}
		return $this;
	}


	/**
	 * @param string $controller
	 * @return $this
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
		return $this;
	}

	/**
	 * @param string $action
	 * @return $this
	 */
	public function setAction($action)
	{
		$this->action = $action;
		return $this;
	}

	/**
	 * @param array[mixed] $params
	 * @return $this
	 */
	public function setParams(array $params)
	{
		$this->params = $params;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRouteString(): string
	{
		return $this->routeString;
	}

	/**
	 * @param string $routeString
	 * @return Route
	 */
	public function setRouteString(string $routeString): Route
	{
		$this->routeString = $routeString;
		return $this;
	}

	/**
	 * @return callable
	 */
	public function getCallbackFunction(): callable
	{
		return $this->callbackFunction;
	}

	/**
	 * @param callable $callbackFunction
	 * @return Route
	 */
	public function setCallbackFunction(callable $callbackFunction): Route
	{
		$this->callbackFunction = $callbackFunction;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isProtected(): bool
	{
		return (bool)$this->protected;
	}

	/**
	 * @param bool $protected
	 * @return Route
	 */
	public function setProtected(bool $protected): Route
	{
		$this->protected = $protected;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 * @return Route
	 */
	public function setOptions(array $options): Route
	{
		$this->options = $options;
		return $this;
	}

	/**
	 * Compares the current route to a supplied route to see if they call the same action.
	 * @param Route $route
	 * @return bool
	 */
	public function sameAction(Route $route): bool
	{
		return $this->getController() === $route->getController() && $this->getAction() === $route->getAction() ? true : false;
	}

	/**
	 * Compares the current route to a supplied route to see if they are the same call.
	 * @param Route $route
	 * @return bool
	 */
	public function same(Route $route): bool
	{
		return $this->getController() === $route->getController() && $this->getAction() === $route->getAction() ? true : false;
	}

	/**
	 * Process an array route
	 * @param array $route
	 * @throws RoutingException
	 */
	protected function processArrayRoute(array $route)
	{
		//Store the route string for later reference.
		$this->setRouteString(implode('/', $route));

		//Set the Controller
		if(count($route) >= 1)
		{
			$controller = array_shift($route);
			//Check route info and convert to method case
			if(ctype_alnum(str_replace('-', '', $controller)) && ctype_alpha(substr($controller, 0, 1)))
			{
				$this->setController(Link::methodCase($controller));
			}
			else
			{
				//Bad info in the route, error out.
				throw new RoutingException('Invalid Route', Error::PAGE_NOT_FOUND);
			}
		}
		else
		{
			$this->setController(self::DEFAULT_CONTROLLER);
		}

		//Set the Action
		if(count($route) >= 1)
		{
			$action = array_shift($route);
			if(ctype_alnum(str_replace('-', '', $action)) && ctype_alpha(substr($action, 0, 1)))
			{
				$this->setAction(Link::methodCase($action));
			}
			elseif(ctype_digit($action))
			{
				$this->setAction(self::DEFAULT_ACTION);
				array_unshift($route, $action);
			}
			else
			{
				//Bad info in the route, error out.
				throw new RoutingException('Invalid Route', Error::PAGE_NOT_FOUND);
			}
		}
		else
		{
			$this->setAction(self::DEFAULT_ACTION);
		}

		//Set Parameters
		if(count($route) >= 1)
		{
			$this->setParams($route);
		}
	}
	
	/**
	 * Process a string-based route
	 * @param string $route
	 * @throws RoutingException
	 */
	protected function processStringRoute($route)
	{
		//Run some route cleaning operations.
		$route = str_replace('\\','/',$route);			//Convert backslashes to forward slashes
		
		//Remove a starting forward slash
		if(substr($route, 0, 1) == '/')	$route = substr($route, 1, strlen($route)-1);
		
		//Remove trailing forward slash
		if(substr($route, (strlen($route)-1), 1) == '/') $route = substr($route, 0, strlen($route)-1);

		//End routing information on the first . ? or # occurrence, process each separately to get the first of any of the objects.
		if(($end = strpos($route,'.')) !== false)
			$route = substr($route, 0, $end);
		if(($end = strpos($route,'?')) !== false)
			$route = substr($route, 0, $end);
		if(($end = strpos($route,'#')) !== false)
			$route = substr($route, 0, $end);

		//Store the route string for later reference.
		$this->setRouteString($route);
		
		//Check to see if a script exists with that route.
		//Split the route into it's component elements.
		$splitRoute = explode('/',$route);
		$routeCount = count($splitRoute);
		
		//If the route only contains a controller add the index action
		if($routeCount == 0 || strlen($route) == 0)
		{
			$splitRoute = array();
			array_push($splitRoute, self::DEFAULT_CONTROLLER);
			array_push($splitRoute, self::DEFAULT_ACTION);
		}
		elseif($routeCount == 1)
		{
			array_push($splitRoute, self::DEFAULT_ACTION);
		}
		elseif($routeCount >= 2)
		{
			//If the action is numeric, it is not the action. Insert the index action into the route.
			if(is_numeric($splitRoute[1]))
			{
				$shift = array_shift($splitRoute);
				array_unshift($splitRoute, $shift, self::DEFAULT_ACTION);
			}
		}
		
		//Check the Controller value and Set a valid value
		$controller = array_shift($splitRoute);
		if(ctype_alnum(str_replace('-', '', $controller)) && ctype_alpha(substr($controller, 0, 1)))
		{
			$this->setController(Link::methodCase($controller));
		}
		else
		{
			//Bad info in the route, error out.
			throw new RoutingException('Invalid Route', Error::PAGE_NOT_FOUND);
		}
		
		//Check the Action Value and Set a valid value
		$action = str_replace(['{','}'], '', array_shift($splitRoute));
		if(ctype_alnum(str_replace(self::ACCEPTABLE_ROUTE_SPECIAL_CHARACTERS, '', $action)) && ctype_alpha(substr($action, 0, 1)))
		{
			$this->setAction(Link::methodCase($action));
		}
		else
		{
			//Bad info in the route, error out.
			throw new RoutingException('Invalid Route', Error::PAGE_NOT_FOUND);
		}
		
		$this->setParams($splitRoute);

		//Don't overwrite a functional route type.
		if(!isset($this->type))
			$this->setType(self::ROUTE_MVC);
	}

	//-------------------------------------FUNCTIONAL ROUTING-------------------------------------

	/**
	 * Add a functional route the the configuration.
	 * @param string $route
	 * @param callable $func
	 * @param bool $protected
	 * @param array $options
	 * @return Route
	 * @throws RoutingException
	 */
	public static function add(string $route, callable $func, bool $protected = false, array $options = []) : Route
	{
		// Valid character check
		if(!self::functionalRouteContainsAllowedCharacters($route))
		{
			throw new RoutingException('Invalid characters in static route.');
		}

		// Check that we are not overwriting a current route.
		if(isset(self::$functionalRoutes[$route]))
		{
			throw new RoutingException('This route already exists and cannot be overwritten during execution.');
		}
		else
		{
			$routeObject = Route::create($route)
				->setType(self::ROUTE_FUNCTIONAL)
				->setProtected($protected)
				->setCallbackFunction($func)
				->setOptions($options);
			self::$functionalRoutes[$routeObject->getRouteString()] = $routeObject;
		}
		return $routeObject;
	}

	/**
	 * Add a callback function to execute before a functional route executes.
	 * @param callable $func
	 */
	public static function before(callable $func)
	{
		self::$beforeRouteCallbacks[] = $func;
	}

	/**
	 * Add a callback function to execute after a functional route executes.
	 * @param callable $func
	 */
	public static function after(callable $func)
	{
		self::$afterRouteCallbacks[] = $func;
	}

	/**
	 * Checks for a match to a registered functional route.
	 * @param mixed $route
	 * @return bool
	 */
	protected static function matchesFunctionalRoute($route)
	{
		try
		{
			$route = self::getFunctionalRouteObject($route);
			if($route instanceof Route)
				return true;
		}
		catch(PageNotFoundException $e) {}

		return false;
	}

	/**
	 * Check to see if the static route matches allowed characters.
	 * @param $route
	 * @return bool
	 */
	protected static function functionalRouteContainsAllowedCharacters($route)
	{
		// Valid character check
		if(ctype_alnum(str_replace(self::ACCEPTABLE_FUNCTION_ROUTE_CHARACTERS,'',$route)))
		{
			return true;
		}
		return false;
	}

	/**
	 * Get the Functional Route object that would match the specified route.
	 * @param mixed $route
	 * @return Route
	 * @throws PageNotFoundException
	 */
	public static function getFunctionalRouteObject($route) : Route
	{
		if(is_array($route))
		{
			$route = implode('/', $route);
		}
		elseif($route instanceof Route)
		{
			$route = $route->getRouteString();
		}

		if(self::functionalRouteContainsAllowedCharacters($route))
		{
			// Exact Route Match
			if(isset(self::$functionalRoutes[$route]))
			{
				return self::$functionalRoutes[$route];
			}
			else
			{
				// @todo Dynamic Routes
			}
		}

		throw new PageNotFoundException('No Route Matches');
	}

	/**
	 * Execute a functional route action passing parameters using call_user_func_array().
	 * @param Route $functionalRoute
	 * @throws RoutingException
	 * @throws \Exception
	 */
	private function dispatchFunctionalRoute(Route $functionalRoute)
	{
		// Execute the Method
		$return = call_user_func_array($functionalRoute->getCallbackFunction(), $functionalRoute->getParams());

		if($return instanceof View)		//Check for a returned View object
		{
			//If the view does not have a controller name set, set it to the currently executing controller.
			if(!$return->hasController())
			{
				$return->setController($this->getController());
			}

			//If the view doesn't have a view set, use the route's action.
			if(!$return->hasView())
			{
				$return->setView($this->getAction());
			}

			//Check for a controller layout and build it.
			//@todo support more than the default layout - View/Layout Refactor
			$layoutName = Config::getValue('layout','default', false);
			if($layoutName != '')
			{
				$layout = new Layout($layoutName);
				$pageSettings = Config::get('page');
				if(array_key_exists('title', $pageSettings))
				{
					$layout->setTitle($pageSettings['title']);
				}
				$layout->build(NULL, $return);
			}
			else
			{
				$return->build();
			}
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
			try
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
			catch (ReflectionException $e)
			{
				throw new RoutingException('Failed Controller Class Reflection', $e->getCode(), $e);
			}
		}
		elseif(is_string($return))		//If the return value was simply a string, echo it out.
		{
			echo $return;
		}
	}

	/**
	 * Performs authentication on functional routes.
	 * @param Route $route
	 * @return bool
	 */
	protected function functionalRouteAuth(Route $route)
	{
		//@todo Implement functional routing authentication.
		return false;
	}

	protected function beforeFunctionalRouting()
	{
		foreach(self::$beforeRouteCallbacks as $func)
		{
			call_user_func($func);
		}
	}

	protected function afterFunctionalRouting()
	{
		foreach(self::$afterRouteCallbacks as $func)
		{
			call_user_func($func);
		}
	}
}
