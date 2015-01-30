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
namespace Staple;

use \Exception, \ReflectionMethod;

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
	protected $errorHander;
	
	/**
	 * Private constructor insures that the application is instantiated as a Singleton.
	 * Application constructor. This function creates a new Staple application. It defines the constants: CONFIG_ROOT, LAYOUT_ROOT,
	 * FORMS_ROOT, MODEL_ROOT, CONTROLLER_ROOT, VIEW_ROOT, and SCRIPT_ROOT. All of these constants exist as folders inside of the
	 * PROGRAM_ROOT directory. The constructor loads and checks configuration, sets up the autoloader, sets custom error handlers
	 * and begins a session.
	 */
	private function __construct()
	{
		//Application Constants, if not already defined
		defined('FOLDER_ROOT')
			|| define('FOLDER_ROOT', realpath(dirname(__FILE__) . '/../'));
		
		defined('LIBRARY_ROOT')
			|| define('LIBRARY_ROOT', FOLDER_ROOT . '/library/');
		
		defined('SITE_ROOT')
			|| define('SITE_ROOT', FOLDER_ROOT . '/public/');
		
		defined('PROGRAM_ROOT')
			|| define('PROGRAM_ROOT', FOLDER_ROOT . '/application/');
		
		defined('ELEMENTS_ROOT')
			|| define('ELEMENTS_ROOT', FOLDER_ROOT . '/elements/');
		
		
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
		$this->setErrorHander(new Error());
		
		//Create a session
		session_start();
		
		//Turn on the timer 
		if(Config::getValue('errors', 'enable_timer') == 1)
		{
			Dev::StartTimer();
		}
	}
	
	/**
	 * Get the error handler for the application.
	 * @return Error $errorHander
	 */
	public function getErrorHander()
	{
		return $this->errorHander;
	}
	
	/**
	 *
	 * @param Error $errorHander        	
	 */
	public function setErrorHander(Error $errorHander)
	{
		$this->errorHander = $errorHander;
		
		//Set the error handlers
		set_error_handler(array($this->errorHander,'handleError'), E_USER_ERROR | E_USER_WARNING | E_WARNING);
		set_exception_handler(array($this->errorHander,'handleException'));
		
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
	}
	
	/**
	 * Returns the current route.
	 * @return $route
	 */
	public function getRoute()
	{
		return $this->route;
	}
	
	/**
	 * @param \Staple\Route $route
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
	 * @return Staple_Controller | NULL
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
	 * 
	 * Executes the application process.
	 */
	public function run($route = NULL)
	{
		//Include the boot file.
		include_once PROGRAM_ROOT.'boot.php';
		
		//Load the controllers from the session.
		if(isset($_SESSION['Staple']['Controllers']))
			if(is_array($_SESSION['Staple']['Controllers']))
				$this->controllers = $_SESSION['Staple']['Controllers'];
		
		//First determine which routing information to use
		if(!is_null($route))								//Use the supplied Route
		{
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
		$this->executeRoute();
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
	 */
	public function registerController(Controller $controller)
	{
		$class_name = substr(get_class($controller),0,strlen(get_class($controller))-10);
		if(!array_key_exists($class_name, $this->controllers))
		{
			$this->controllers[$class_name] = $controller;
			return $controller;
		}
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