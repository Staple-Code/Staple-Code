<?PHP
/**
 * The main class for the STAPLE Framwork, Staple_Main creates the application and
 * coordinates all the other modules. Using this object will require the entire
 * framework code to be available. Many other modules can stand apart with varying
 * levels of autonomy.
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

class Main
{
	/**
	 * 
	 * The instance property holds the singleton instance for Staple_Main
	 * @var Main
	 */
	protected static $instance;
	
	/**
	 * Holds the current route object
	 * @var Route
	 */
	protected $route;
	
	/**
	 * Holds references to the current instantiated controllers
	 * @var array[Controller]
	 */
	protected $controllers = array();
	
	/**
	 * The autoloader class instance
	 * @var Autoload
	 */
	protected $loader;
	
	/**
	 * Instance of the error handler object
	 * @var Error
	 */
	protected $errorHandler;
	
	/**
	 * Private constructor insures that the application is instantiated as a Singleton.
	 * Application constructor. This function creates a new Staple application. It defines the constants: CONFIG_ROOT, LAYOUT_ROOT,
	 * FORMS_ROOT, MODEL_ROOT, CONTROLLER_ROOT, VIEW_ROOT, and SCRIPT_ROOT. All of these constants exist as folders inside of the
	 * APPLICATION_ROOT directory. The constructor loads and checks configuration, sets up the autoloader, sets custom error handlers
	 * and begins a session.
	 */
	private function __construct()
	{
		//Application Constants, if not already defined
		defined('FOLDER_ROOT')
			|| define('FOLDER_ROOT', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));
		
		defined('LIBRARY_ROOT')
			|| define('LIBRARY_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR);
		
		defined('SITE_ROOT')
			|| define('SITE_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
		
		defined('APPLICATION_ROOT')
			|| define('APPLICATION_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
		
		defined('MODULES_ROOT')
			|| define('MODULES_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR);

		defined('VENDOR_ROOT')
			|| define('VENDOR_ROOT', FOLDER_ROOT . DIRECTORY_SEPARATOR . 'vendor'. DIRECTORY_SEPARATOR);
		
		//Setup STAPLE Constants
		defined('CONFIG_ROOT')
	    	|| define('CONFIG_ROOT', APPLICATION_ROOT . 'config' . DIRECTORY_SEPARATOR);

	    defined('LAYOUT_ROOT')
			|| define('LAYOUT_ROOT', APPLICATION_ROOT . 'layouts' . DIRECTORY_SEPARATOR);
		
		defined('FORMS_ROOT')
			|| define('FORMS_ROOT', APPLICATION_ROOT . 'forms' . DIRECTORY_SEPARATOR);

		defined('MODEL_ROOT')
			|| define('MODEL_ROOT', APPLICATION_ROOT . 'models' . DIRECTORY_SEPARATOR);

		defined('CONTROLLER_ROOT')
			|| define('CONTROLLER_ROOT', APPLICATION_ROOT . 'controllers' . DIRECTORY_SEPARATOR);

		defined('STATIC_ROOT')
			|| define('STATIC_ROOT', APPLICATION_ROOT . 'static' . DIRECTORY_SEPARATOR);

		defined('VIEW_ROOT')
			|| define('VIEW_ROOT', APPLICATION_ROOT . 'views' . DIRECTORY_SEPARATOR);

		defined('SCRIPT_ROOT')
			|| define('SCRIPT_ROOT',FOLDER_ROOT . 'scripts' . DIRECTORY_SEPARATOR);
		
		defined('STAPLE_ROOT')
			|| define('STAPLE_ROOT',LIBRARY_ROOT . 'Staple' . DIRECTORY_SEPARATOR);
		
		//Include the Staple Config and Alias class always
		require_once STAPLE_ROOT.'Alias.class.php';
		require_once STAPLE_ROOT.'Config.class.php';
		
		//Alias the primary classes
		Alias::load('Alias', false);
		Alias::load('Config', false);
		Alias::load('Main', false);
		
		//Check for a custom loader
		if(Config::getValue('application', 'loader') != '')
		{
		    $loader = Config::getValue('application', 'loader');
		    
		    //Create Temporary loader if the class does not exist.
			if(!class_exists($loader))
			{
			    require_once STAPLE_ROOT.'Autoload.class.php';
			    $tmpLoader = new Autoload();
			    $tmpLoader->load($loader);
			}
			
			//Instantiate custom loader
			$loader = new $loader();
			if($loader instanceof Autoload)
			{
				$this->loader = $loader;
			}
		}
		
		//If no other loader is found or set, use the Staple_Autoload class
		if(!($this->loader instanceof Autoload))
		{
		    require_once STAPLE_ROOT.'Autoload.class.php';
			$this->loader = new Autoload();
		}
		
		//Register the Autoload class
		spl_autoload_register(array($this->loader, 'load'));
			
		// Setup Error Handlers
		$this->setErrorHandler(new Error());
		
		//Create a session
		if(php_sapi_name() != 'cli')
			session_start();
		
		//Turn on the timer 
		if(Config::findOrNull('errors', 'enable_timer') == 1)
		{
			Dev::startTimer();
		}

		//Include the boot file.
		if(file_exists(APPLICATION_ROOT.'boot.php'))
			include_once APPLICATION_ROOT.'boot.php';
	}
	
	/**
	 * Get the error handler for the application.
	 * @return Error $errorHandler
	 */
	public function getErrorHandler()
	{
		return $this->errorHandler;
	}
	
	/**
	 * @param Error $errorHandler
	 * @return Main
	 */
	public function setErrorHandler(Error $errorHandler)
	{
		$this->errorHandler = $errorHandler;
		
		//Set the error handlers
		set_error_handler(array($this->errorHandler,'handleError'), E_USER_ERROR | E_USER_WARNING | E_WARNING);
		set_exception_handler(array($this->errorHandler,'handleException'));
		
		return $this;
	}
	
	public function inDevMode()
	{
	    return (bool)Config::getValue('errors', 'devmode');
	}

	/**
	 * The application destructor stored controllers in the session to preserve their state
	 */
	public function __destruct()
	{
		$_SESSION['Staple']['Controllers'] = $this->controllers;		//Store the controllers in the session
	}
	
	/**
	 * 
	 * Instantiates the application as a singleton, and/or returns the current instance.
	 */
	public static function get()
	{
		if (!(self::$instance instanceof Main)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
		return self::$instance;
	}
	
	/**
	 * Get or set a controller on the Main object
	 * @param Controller | string $class
	 * @return Controller | NULL
	 */
	public static function controller($class)
	{
		if($class instanceof Controller)
		{
			return Main::get()->registerController($class);
		}
		elseif(is_string($class))
		{
			return Main::get()->getController($class);
		}

		return NULL;
	}
	
	/**
	 * Returns the current route.
	 * @return Route $route
	 */
	public function getRoute()
	{
		return $this->route;
	}
	
	/**
	 * @param \Staple\Route $route
	 * @return Main
	 */
	public function setRoute(Route $route)
	{
		$this->route = $route;
		return $this;
	}

	/**
	 * Return the current route action at is executing.
	 * @return string | NULL
	 */
	public function getRouteAction()
	{
		if(isset($this->route))
		{
			if($this->route instanceof Route)
			{
				return $this->route->getAction();
			}
		}
		return NULL;
	}
	
	/**
	 * Returns a reference to a controller object
	 * 
	 * @param string $class
	 * @return Controller | NULL
	 */
	public function getController($class)
	{
		if(isset($this->controllers[$class]))
		{
			return $this->controllers[$class];
		}
		else return NULL;
	}
	
	/**
	 * Executes the application process.
	 * @param Route | string $route
	 * @return bool
	 */
	public function run($route = NULL)
	{
		//Load the controllers from the session.
		if(isset($_SESSION['Staple']['Controllers']))
			if(is_array($_SESSION['Staple']['Controllers']))
				$this->controllers = $_SESSION['Staple']['Controllers'];
		
		//First determine which routing information to use
		if(!is_null($route))								//Use the supplied Route
		{
			if($route instanceof Route)
				$initialRoute = $route;
			else
				$initialRoute = new Route($route);
		}
		elseif(array_key_exists('REQUEST_URI', $_SERVER))		//Use the URI route
		{
			$initialRoute = new Route(urldecode($_SERVER['REQUEST_URI']));
		}
		elseif(array_key_exists('PATH_INFO', $_SERVER))		//Use the PATH_INFO route
		{
			$initialRoute = new Route(urldecode($_SERVER['PATH_INFO']));
		}
		else												//Use the default route
		{
			$initialRoute = new Route('/');
		}
		
		//Run the route through the router.
		$this->setRoute($initialRoute);
		return $this->executeRoute();
	}
	
	/**
	 * Execute the current route
	 * @return boolean
	 */
	protected function executeRoute()
	{
		if($this->route instanceof Route)
		{
			return $this->route->execute();
		}

		return false;
	}
	
	/**
	 * 
	 * This function creates an internal redirection. It accepts the
	 * redirect as a routing string. This can be generated using the Staple_Link::get() function.
	 * 
	 * @param mixed $newRoute
	 */
	public function redirect($newRoute)
	{
		$this->setRoute(Route::make($newRoute));
		$this->executeRoute();
		exit(0);
	}
	/**
	 * Registers a controller that was instantiated outside of the Staple_Main class.
	 * @param Controller $controller
	 * @return Controller
	 */
	public function registerController(Controller $controller)
	{
		$class_name = substr(get_class($controller),0,strlen(get_class($controller))-10);
		if(!array_key_exists($class_name, $this->controllers))
		{
			$this->controllers[$class_name] = $controller;
		}
		return $controller;
	}
	/**
	 * @return Autoload $loader
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * @param Autoload $loader
	 */
	public function setLoader(Autoload $loader)
	{
		$this->loader = $loader;
	}

}