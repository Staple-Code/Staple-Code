<?PHP
/**
 * The main class for the STAPLE Framwork, Staple_Main creates the application and
 * coordinates all the other modules. Using this object will require the entire
 * framework code to be available. Many other modules can stand apart with varying
 * levels of autonomy.
 * 
 * @author Ironpilot
 * @copyright Copywrite (c) 2011, STAPLE CODE
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
class Staple_Main
{
	const DONT_THROW_LOADER_ERRORS = 504;
	const DEFAULT_AUTOLOAD_CLASS = 'Staple_Autoload';
	/**
	 * 
	 * The instance property holds the singleton instance for Staple_Main
	 * @var Staple_Main
	 */
	protected static $instance;
	
	/**
	 * 
	 * Settings array
	 * @var array
	 */
	protected $settings = array();
	
	/**
	 * 
	 * Holds a reference to the database object
	 * @var Staple_DB
	 */
	protected $db;
	
	/**
	 * 
	 * Holds the current route
	 * @var string
	 */
	protected static $route;
	
	/**
	 * Holds the route executed on the last page call.
	 * @var string
	 */
	protected static $referrer;
	
	/**
	 * Holds references to the current instantiated controllers
	 * @var array of Staple_Controller
	 */
	protected static $controllers = array();
	
	/**
	 * Holds a reference to the Staple_Auth class
	 * @var Staple_Auth
	 */
	private $auth;
	/**
	 * The autoloader class instance
	 * @var Staple_Autoload
	 */
	protected $loader;
	/**
	 * Instance of the error handler object
	 * @var Staple_Error
	 */
	protected $errorHander;
	/**
	 * 
	 * Private constructor insures that the application is instantiated as a Singleton
	 */
	
	private $headersent = false;
	private $footersent = false;
	
	/**
	 * 
	 * Application constructor. This function creates a new Staple application. It defines the constants: CONFIG_ROOT, LAYOUT_ROOT,
	 * FORMS_ROOT, MODEL_ROOT, CONTROLLER_ROOT, VIEW_ROOT, and SCRIPT_ROOT. All of these constants exist as folders inside of the
	 * PROGRAM_ROOT directory. The constructor loads and checks configuration, sets up the autoloader, sets custom error handlers,
	 * starts output buffering and begins a session.
	 */
	private function __construct()
	{
		//Setup STAPLE Constants
		defined('CONFIG_ROOT')
	    	|| define('CONFIG_ROOT', PROGRAM_ROOT . 'config/');

	    defined('LAYOUT_ROOT')
			|| define('LAYOUT_ROOT', PROGRAM_ROOT . 'layouts/');
		
		defined('FORMS_ROOT')
			|| define('FORMS_ROOT', PROGRAM_ROOT . 'forms/');
			
		defined('MODEL_ROOT')
			|| define('MODEL_ROOT', PROGRAM_ROOT . 'models/');
			
		defined('CONTROLLER_ROOT')
			|| define('CONTROLLER_ROOT', PROGRAM_ROOT . 'controllers/');
			
		defined('VIEW_ROOT')
			|| define('VIEW_ROOT', PROGRAM_ROOT . 'views/');
			
		defined('SCRIPT_ROOT')
			|| define('SCRIPT_ROOT',PROGRAM_ROOT . 'scripts/');
		
		defined('STAPLE_ROOT')
			|| define('STAPLE_ROOT',LIBRARY_ROOT . 'Staple/');
		
		//Parse the settings file
		$this->settings = parse_ini_file(CONFIG_ROOT.'application.ini',true);
		$this->checkSettings();
		
		//Include, create and set the autoloader
		
		//Include the Staple Autoload class always
		require_once STAPLE_ROOT.'Autoload.class.php';
		
		//Check for a custom loader
		if(array_key_exists('loader', $this->settings['application']))
		{
			if(class_exists($this->settings['application']['loader']))
			{
				$loader = new $this->settings['application']['loader']();
				if($loader instanceof Staple_Autoload)
				{
					$this->loader = $loader;
					spl_autoload_register(array($this->loader, 'load'));
				}
			}
		}
		
		//If no other loader is found or set, use the Staple_Autoload class
		if(!($this->loader instanceof Staple_Autoload))
		{
			$this->loader = new Staple_Autoload();
			spl_autoload_register(array($this->loader, 'load'));
		}
			
		// Setup Error Handlers
		$this->setErrorHander(new Staple_Error());
		
		//Start Output buffering
		ob_start();
		
		//Create a session
		session_start();
		if($this->settings['errors']['devmode'] == 1)
		{
			Staple_Dev::StartTimer();
		}
	}
	
	/**
	 *
	 * @return Staple_Error $errorHander
	 */
	public function getErrorHander()
	{
		return $this->errorHander;
	}
	
	/**
	 *
	 * @param Staple_Error $errorHander        	
	 */
	public function setErrorHander(Staple_Error $errorHander)
	{
		$this->errorHander = $errorHander;
		
		set_error_handler(array(
				$this->errorHander,
				'handleError'
		), E_USER_ERROR | E_USER_WARNING | E_WARNING);
		set_exception_handler(array(
				$this->errorHander,
				'handleException'
		));
		
		return $this;
	}
	
	public function inDevMode()
	{
	    return (bool)Staple_Config::getValue('errors', 'devmode');
	}

	/**
	 * The application destructor renders the footer of the website and flushes the 
	 * output buffer.
	 */
	public function __destruct()
	{
		$this->processFooter();
		$_SESSION['Staple']['Controllers'] = self::$controllers;		//Store the controllers in the session
		$_SESSION['Staple']['Main']['Referrer'] = self::$route;			//Store the last executed route
		ob_end_flush();
		/*if(Staple_Config::getValue('errors','devmode') == 1) //@todo use "register_shutdown_function" to accomplish this.
		{
			echo '<!-- Execution Time: '.Staple_Dev::StopTimer()." -->\n";
			echo '<!-- Memory Usage: '.number_format(memory_get_peak_usage(true)).' bytes -->';
		}*/
	}
	
	/**
	 * 
	 * Instantiates the application as a singleton, and/or returns the current instance.
	 */
	public static function get()
	{
		if (!(self::$instance instanceof Staple_Main)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
		return self::$instance;
	}
	
	/**
	 * Returns the current route.
	 * @return $route
	 */
	public static function getRoute()
	{
		return self::$route;
	}
	
	public static function getRouteAction()
	{
		$route = explode('/', self::$route);
		if(count($route) == 0)
		{
			return 'index/index';
		}
		elseif(count($route) == 1)
		{
			return $action = $route[0].'/index';
		}
		else
		{
			$action = $route[0].'/'.$route[1];
			return $action;
		}
	}
	
	public static function getReferrer()
	{
		return self::$referrer;
	}
	
	/**
	 * Returns a reference to a controller object
	 * 
	 * @param string $class
	 * @return Staple_Controller | NULL
	 */
	public static function getController($class)
	{
		if(array_key_exists($class, self::$controllers))
		{
			return self::$controllers[$class];
		}
		else
		{
			/*$controllerClass = $class.'Controller';
			if(class_exists($controllerClass))
			{
				self::$controllers[$class] = new $controllerClass();
				self::$controllers[$class]->_start();
				return self::$controllers[$class];
			}
			else
			{*/
				return NULL;
			//}
		}
	}
	
	/**
	 * 
	 * Executes the application process.
	 */
	public function run($directive = NULL)
	{
		//Create and enable site-wide authorization.
		if(array_key_exists('enabled', $this->settings['auth']))
			if($this->settings['auth']['enabled'] == 1)
				$this->auth = Staple_Auth::get();
		
		//Create and connect to the database.
		if(array_key_exists('autoconnect', $this->settings['db']))
			if($this->settings['db']['autoconnect'] == 1)
				$this->db = Staple_DB::get();
		
		//Load the controllers from the session.
		if(array_key_exists('Staple', $_SESSION))
			if(array_key_exists('Controllers', $_SESSION['Staple']))
				if(is_array($_SESSION['Staple']['Controllers']))
					self::$controllers = $_SESSION['Staple']['Controllers'];
					
		//Load the referring route from the session.
		if(array_key_exists('Staple', $_SESSION))
			if(array_key_exists('Main', $_SESSION['Staple']))
				if(array_key_exists('Referrer',$_SESSION['Staple']['Main']))
					self::$referrer = $_SESSION['Staple']['Main']['Referrer'];
		
		//Processes Initialization Directives
		if(isset($directive))
		{
			switch($directive)
			{
				case self::DONT_THROW_LOADER_ERRORS:
					$this->loader->setThrowOnFailure(false);
					break;
			}
		}		
		
		//Run the route through the router.
		$this->route();
	}
	
	/**
	 * Draws the header of the site, if found.
	 * @deprecated
	 */
	public function processHeader($force = false)
	{
		
		//Create the site header if used. 
		if(array_key_exists('header', $this->settings['page']))
		{
			if($this->settings['page']['header'] != '')
			{
				$headerFile = $this->settings['page']['header'];
				if(file_exists($headerFile))
				{
					if(!($this->headersent) || $force === true)
					{
						include $headerFile;
						$this->headersent = true;
					}
				}
				else
				{
					throw new Exception('Invalid Header Location', Staple_Error::APPLICATION_ERROR);
				}
			}
		}
	}
	
	/**
	 * Draws the footer of the site, if found.
	 * @deprecated
	 */
	public function processFooter()
	{
		if(array_key_exists('footer', $this->settings['page']))
		{
			if($this->settings['page']['footer'] != '')
			{
				$footerFile = $this->settings['page']['footer'];
				if(file_exists($footerFile))
				{
					if(!($this->footersent))
					{
						include $footerFile;
						$this->footersent = true;
					}
				}
				else
				{
					throw new Exception('Invalid Footer Location', Staple_Error::APPLICATION_ERROR);
				}
			}
		}
	}
	
	/**
	 * 
	 * Main routing function for the application
	 * @todo Delegate to Staple_Route
	 * @param string $route
	 * @throws Exception
	 */
	public function route($route = NULL)
	{		
		//First determine which routing information to use
		if(!is_null($route))								//Use the supplied Route
		{
			self::$route = $route;
		}
		elseif(array_key_exists('PATH_INFO', $_SERVER))		//Use the URI route
		{
			self::$route = $_SERVER['PATH_INFO']; 
			self::$route = urldecode(self::$route);			//URL decode any special characters
			if(strpos(self::$route, '?') !== false)
			{
				self::$route = substr(self::$route, 0, strpos(self::$route, '?'));
			}
			if(strlen(self::$route) == 0 || self::$route == '/')
			{
				self::$route = 'index/index';
			}
		}
		else												//Use the default route
		{
			self::$route = 'index/index';
		}
		
		//Run some route cleaning operations.
		self::$route = str_replace('\\','/',self::$route);			//Convert backslashes to forward slashes 
		if(substr(self::$route, 0, 1) == '/')						//Remove a starting forward slash
		{
			self::$route = substr(self::$route, 1, strlen(self::$route)-1);
		}
		if(($end = strpos(self::$route,'.')) !== false)				//End routing information on the first "." occurance
		{
			self::$route = substr(self::$route, 0, $end);
		}
		
		//echo '<p>Current Route: '.self::$route.'</p>';
		
		//Check to see if a script exists with that route.
		$scriptRoute = SCRIPT_ROOT.self::$route.'.php';
		if(file_exists($scriptRoute))
		{
			//Check for valid path information
			if(ctype_alnum(str_replace(array('/','_','-'),'',self::$route)))
			{
				//Authentication Check
				if(Staple_Config::getValue('auth', 'enabled') != 0)
				{
					//Check for an excluded script route
					$allowedScripts = (array)Staple_Config::getValue('auth', 'allowedRoute');
					if(in_array(self::$route, $allowedScripts) === true)
					{
						//Script does not require auth, Dispatch Script
						$this->dispatchScript($scriptRoute);
					}
					else
					{
						//Check for a login
						if($this->auth->isAuthed() === true)
						{
							//Valid login found, Dispatch Script
							$this->dispatchScript($scriptRoute);
						}
						else
						{
							//No valid login, no Auth
							$this->auth->noAuth();
						}
					}
				}
				else
				{
					//Auth Disabled, Dispatch Script
					$this->dispatchScript($scriptRoute);
				}
				return true;
			}
		}
		else
		{
			//No Script found, routing to controller/action
			
			//Split the route into it's component elements.
			$splitRoute = explode('/',self::$route);
			
			//If the route only contains a controller add the index action
			if(count($splitRoute) == 1)
			{
				array_push($splitRoute, 'index');
			}
			elseif(count($splitRoute) >= 2)
			{
				//Correct for extra ending slash.
				if(strlen($splitRoute[1]) < 1)
				{
					$splitRoute[1] = 'index';
				}
				//If the action is numeric, it is not the action. Insert the index action into the route.
				if(is_numeric($splitRoute[1]))
				{
					$shift = array_shift($splitRoute);
					array_unshift($splitRoute, $shift, 'index');
				}
			}
			$class = Staple_Link::methodCase(array_shift($splitRoute));
			$method = Staple_Link::methodCase(array_shift($splitRoute));
			if(ctype_alnum($class) && ctype_alnum($method))
			{
				$dispatchClass = $class.'Controller';
				$started = false;
			
				//Check for the controller existence
				if(class_exists($dispatchClass))
				{
					//Check for the action existence
					if(method_exists($dispatchClass, $method))
					{
						//If the controller has not been created yet, create an instance and store it in the front controller
						if(!array_key_exists($class, self::$controllers))
						{
							self::$controllers[$class] = new $dispatchClass();
							self::$controllers[$class]->_start();
							$started = true;
						}
						
						//Verify that an instance of the controller class exists and is of the right type
						if(self::$controllers[$class] instanceof Staple_Controller)
						{
							//Check if global Auth is enabled.
							if(Staple_Config::getValue('auth', 'enabled') != 0)
							{
								//Check the sub-controller for access to the method
								if(self::$controllers[$class]->_auth($method) === true)
								{
									$this->dispatchController($class, $method, $splitRoute, $started);
								}
								else
								{
									$this->auth->noAuth();
								}
							}
							else
							{
								$this->dispatchController($class, $method, $splitRoute, $started);
							}
							return true;
						}
					}
				}
			}
		}
		
		//If a valid page cannot be found, throw page not found exception
		throw new Exception('Page Not Found',Staple_Error::PAGE_NOT_FOUND);
	}
	
	/**
	 * 
	 * Function executes a controller action passing parameters using call_user_func_array().
	 * It also builds the view for the route.
	 * 
	 * @param string $class
	 * @param string $method
	 * @param array $params
	 */
	protected function dispatchController($controller,$action, array $params, $started = false)
	{
		if($started !== true)
		{
			//Start up the controller
			call_user_func(array(self::$controllers[$controller],'_start'));
		}
		
		//Set the view's controller to match the route
		self::$controllers[$controller]->view->setController($controller);
		
		//Set the view's action to match the route
		self::$controllers[$controller]->view->setView($action);
		
		//Call the controller action
		$actionMethod = new ReflectionMethod(self::$controllers[$controller],$action); 
		$actionMethod->invokeArgs(self::$controllers[$controller], $params);
		//call_user_func_array(array(self::$controllers[$controller],$action), $params);
		
		//Grab the buffer contents from the controller and post it after the header.
		$buffer = ob_get_contents();
		ob_clean();
		
		//Process the header
		$this->processHeader();
		
		if(self::$controllers[$controller]->layout instanceof Staple_Layout)
		{
			self::$controllers[$controller]->layout->build($buffer);
		}
		else
		{
			echo $buffer;
			self::$controllers[$controller]->view->build();
		}
	}
	
	/**
	 * This function runs the dispatch for a given script route.
	 * 
	 * @param string $route
	 */
	protected function dispatchScript($route)
	{
		//Create a blank layout
		$layout = new Staple_Layout();
		
		//Find the default Layout
		$defaultLayout = Staple_Config::getValue('layout', 'default');
		
		//Setup the default layout
		if($defaultLayout != '') $layout->setName($defaultLayout);
		
		//run the script
		require $route;
		
		//Grab the buffer contents from the controller and post it after the header.
		$buffer = ob_get_contents();
		ob_clean();
		
		//Process the Header
		$this->processHeader();
		
		if($layout->getName() != '')
		{
			//Build the Layout
			$layout->build($buffer);
		}
		else
		{
			//Echo the Buffer
			echo $buffer;
		}
	}
	
	/**
	 * The purpose of this function is to dispatch an action/view and return the results in a string.
	 * Any errors that occur will return a boolean false.
	 * @return string | boolean
	 */
	public function pocketDispatch(Staple_Route $route)
	{
		//@todo complete the function
	}
	
	/**
	 * @todo This function not implemented yet
	 */
	protected function checkSettings()
	{
		//Check the settings array
	}
	
	/**
	 * This function has become a helper of the Staple_Link object. Returns a link from 
	 * the specified link parameters.
	 * 
	 * @see Staple_Link::get()
	 */
	public function link($route, array $get = array())
	{
		return Staple_Link::get($route,$get);
	}
	
	/**
	 * Removed - no more baselinks....
	 * Returns a urlencoded link relative to the public base of the website.
	 * @param string $link
	 */
	/*public function baseLink($link,$get = NULL)
	{
		return htmlentities($this->settings['application']['public_location'].$link.$get);
	}*/
	
	/**
	 * 
	 * This function creates an internal redirection within the script itself. It accepts the
	 * redirect as a routing string. This can be generated using the Staple_Link::get() function.
	 * 
	 * @link Staple_Link::get()
	 * 
	 * @param mixed $newRoute
	 */
	public function redirect($newRoute)
	{
		ob_clean();
		$this->route(Staple_Link::get($newRoute));
		exit(0);
	}
	/**
	 * 
	 * Registers a controller that was instantiated outside of the Staple_Main class.
	 * @param Staple_Controller $controller
	 */
	public function registerController(Staple_Controller $controller)
	{
		$class_name = substr(get_class($controller),strlen(get_class($controller))-10,10);
		if(!array_key_exists($class_name, self::$controllers))
		{
			self::$controllers[$class_name] = $controller;
		}
	}
	public function excludeHeaderFooter()
	{
		$this->headersent = true;
		$this->footersent = true;
	}
	/**
	 * @return the $loader
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * @param Staple_Autoload $loader
	 */
	public function setLoader(Staple_Autoload $loader)
	{
		$this->loader = $loader;
	}

}