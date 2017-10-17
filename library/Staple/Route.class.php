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
use Staple\Auth\Auth;
use Staple\Controller\Controller;
use Staple\Controller\RestfulController;
use Staple\Exception\PageNotFoundException;
use Staple\Exception\RoutingException;

class Route
{
	const ROUTE_MVC = 1;
	const ROUTE_SCRIPT = 2;
	const CONTROLLER_SUFFIX = "Controller";
	const PROVIDER_SUFFIX = "Provider";
	const DEFAULT_ACTION = 'index';
	const DEFAULT_CONTROLLER = 'index';

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
	
	public function __construct($link = NULL)
	{
		if(is_array($link))
		{
			$this->processArrayRoute($link);
		}
		else
		{
			$this->processStringRoute($link);
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
	 */
	public static function create($link = NULL)
	{
		return new static($link);
	}

	/**
	 * Execute the route
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
	 */
	protected function dispatchController()
	{
		$controller = Main::controller($this->getController());
		
		if($controller instanceof Controller)
		{
			//Call the controller action
			$actionMethod = new ReflectionMethod($controller,$this->getAction());
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
				//If the object is stringable, covert it to a string and output it.
				$class = new ReflectionClass($return);
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
	 */
	private function routeToProvider($providerClass)
	{
		$providerObject = new $providerClass();
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
	 * @param number $type
	 * @return $this
	 */
	public function setType($type)
	{
		switch($type)
		{
			case self::ROUTE_MVC:
			case self::ROUTE_SCRIPT:
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
	 * Process an array route
	 * @param array $route
	 * @throws RoutingException
	 */
	protected function processArrayRoute(array $route)
	{
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
		$action = array_shift($splitRoute);
		if(ctype_alnum(str_replace('-', '', $action)) && ctype_alpha(substr($action, 0, 1)))
		{
			$this->setAction(Link::methodCase($action));
		}
		else
		{
			//Bad info in the route, error out.
			throw new RoutingException('Invalid Route', Error::PAGE_NOT_FOUND);
		}
		
		$this
			->setParams($splitRoute)
			->setType(self::ROUTE_MVC);
	}
}
