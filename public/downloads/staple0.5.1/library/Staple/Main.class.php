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
	 * 
	 * Holds the route executed on the last page call.
	 * @var string
	 */
	protected static $referrer;
	
	/**
	 * 
	 * Holds references to the current instantiated controllers
	 * @var array of Staple_Controller
	 */
	protected static $controllers = array();
	
	/**
	 * 
	 * Holds a reference to the Staple_Auth class
	 * @var Staple_Auth
	 */
	private $auth;
	
	/**
	 * 
	 * Private constructor insures that the application is instantiated as a Singleton
	 */
	
	private $headersent = false;
	private $footersent = false;
	
	private function __construct()
	{
		$this->settings = parse_ini_file(CONFIG_ROOT.'application.ini',true);
		$this->checkSettings();
		include(LIBRARY_ROOT.'/Staple/Autoload.class.php');
		spl_autoload_register('Staple_Autoload::load');
		set_error_handler('Staple_Error::handleError',E_USER_ERROR | E_USER_NOTICE | E_USER_WARNING | E_WARNING);
		set_exception_handler('Staple_Error::handleException');
		ob_start();
		session_start();
	}
	
	/**
	 * The application destructor renders the footer of the website and flushes the 
	 * output buffer.
	 */
	public function __destruct()
	{
		$this->processFooter();
		$_SESSION['Staple']['Controllers'] = self::$controllers;
		$_SESSION['Staple']['Main']['Referrer'] = self::$route;
		ob_end_flush();
	}
	
	/**
	 * 
	 * Instantiates the application as a singleton, or returns the current instance.
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
	 * Returns the current script route.
	 * @return $route
	 */
	public static function getRoute()
	{
		return self::$route;
	}
	
	public static function getReferrer()
	{
		return self::$referrer;
	}
	
	public static function getController($class)
	{
		if(array_key_exists($class, self::$controllers))
		{
			return self::$controllers[$class];
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * 
	 * Executes the application process.
	 */
	public function run()
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
		
		//Run the route through the router.
		$this->route();
	}
	
	/**
	 * 
	 * Draws the header of the site, if found.
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
	 * 
	 * Draws the footer of the site, if found.
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
	 * @param string $route
	 * @throws Exception
	 */
	public function route($route = NULL)
	{
		if(!is_null($route))
		{
			self::$route = $route;
		}
		elseif(array_key_exists('PATH_INFO', $_SERVER))
		{
			self::$route = str_replace('\\','/',$_SERVER['PATH_INFO']);
			if(substr(self::$route, 0, 1) == '/')
			{
				self::$route = substr(self::$route, 1, strlen(self::$route)-1);
			}
		}
		else
		{
			self::$route = 'index/index';
		}
		
		
		
		//echo '<p>Current Route: '.self::$route.'</p>';
		
		/*
		 * This doesn't work anyway....
		for($i = 0; $i < min(2,count(self::$route)); $i++)
		{
			if(!ctype_alnum(str_replace('/','',self::$route[$i])))
			{
				throw new Exception('Routing Error',Staple_Error::PAGE_NOT_FOUND);
			}
		}*/
		$scriptRoute = PROGRAM_ROOT.'scripts/'.self::$route.'.php';
		if(file_exists($scriptRoute))
		{
			if(!ctype_alnum(str_replace('/','',self::$route)))
			{
				throw new Exception('Routing Error',Staple_Error::PAGE_NOT_FOUND);
			}
			if(array_search(self::$route, $this->settings['auth']['protectRoute']) === false)
			{
				$this->dispatchScript($scriptRoute);
			}
			else 
			{
				if($this->auth->getAuth())
				{
					$this->dispatchScript($scriptRoute);
				}
				else 
				{
					$this->auth->doAuth();
				}
			}
		}
		else
		{
			$splitRoute = explode('/',self::$route);
			for($i = 0; $i < min(2,count($splitRoute)); $i++)
			{
				if(!ctype_alnum($splitRoute[$i]))
				{
					throw new Exception('Routing Error',Staple_Error::PAGE_NOT_FOUND);
				}
			}
			if(count($splitRoute) == 1)
			{
				array_push($splitRoute, 'index');
			}
			if(count($splitRoute) >= 2)
			{
				if(is_numeric($splitRoute[1]))
				{
					$splitCont = array_shift($splitRoute);
					$splitCont .= '/index/';
					$splitCont .= implode('/', $splitRoute);
					unset($splitRoute);
					$splitRoute = explode('/',$splitCont);
				}
			}
			$class = array_shift($splitRoute);
			$dispatchClass = $class.'Controller';
			$method = array_shift($splitRoute);
			if(class_exists($dispatchClass))
			{
				if(method_exists($dispatchClass, $method))
				{
					if(!array_key_exists($class, self::$controllers))
					{
						self::$controllers[$class] = new $dispatchClass();
					}
					if(self::$controllers[$class] instanceof Staple_Controller)
					{
						if(array_key_exists('enabled', $this->settings['auth']))
						{
							if($this->settings['auth']['enabled'] == 1)
							{
								if(self::$controllers[$class]->_auth($method) && self::$controllers[$class]->_authLevel($method) == 0)
								{
									$this->dispatchController($class, $method, $splitRoute);
								}
								else
								{
									if($this->auth->isAuthed() && $this->auth->getAuthLevel() >= self::$controllers[$class]->_authLevel($method))
									{
										$this->dispatchController($class, $method, $splitRoute);
									}
									else 
									{
										$this->auth->noAuth();
									}
								}
							}
							else
							{
								$this->dispatchController($class, $method, $splitRoute);
							}
						}
						else
						{
							$this->dispatchController($class, $method, $splitRoute);
						}
					}
					else 
					{
						throw new Exception('Page Not Found',Staple_Error::PAGE_NOT_FOUND);
					}
				}
				else
				{
					throw new Exception('Page Not Found',Staple_Error::PAGE_NOT_FOUND);
				}
			}
			else
			{
				throw new Exception('Page Not Found',Staple_Error::PAGE_NOT_FOUND);
			}
			
		}
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
	protected function dispatchController($class,$method, array $params)
	{
		$this->processHeader();
		call_user_func_array(array(self::$controllers[$class],$method), $params);
		self::$controllers[$class]->view->build($class,$method);
	}
	
	/**
	 * 
	 * This function runs the dispatch for a given script route.
	 * 
	 * @param string $route
	 */
	protected function dispatchScript($route)
	{
		$this->processHeader();
		include $route;
	}
	
	protected function checkSettings()
	{
		//Check the settings array
	}
	
	/**
	 * 
	 * Creates the shortest link possible to the specified controller/action. 
	 * The array must include a controller, followed by an action, followed by any parameters
	 * sent to the action as unique items in the array. Parameters may only be of type string
	 * or int.
	 * 
	 * @param array $route
	 * @param array $get
	 */
	public function link($route, array $get = array())
	{
		$getString = '';
		foreach($get as $gkey=>$gvalue)
		{
			if($getString == '')
			{
				$getString = '?';
			}
			$getString .= urlencode($gkey).'='.urlencode($gvalue).'&';
		}
		if(substr($getString, strlen($getString)-1,1) == '&')
		{
			
			$getString = substr($getString,0,strlen($getString)-1);
		}
		if(!is_array($route))
		{
			return $this->baseLink($route,$getString);
		}
		else
		{
			//var_dump($route);
			//TODO: add a check for the public location field.
			$base = $this->settings['application']['public_location'];
			$routesize = count($route);
			if($routesize == 0)
			{
				return $base.$getString;
			}
			for($i = 0; $i < count($route); $i++)
			{
				if($i <= 1)
				{
					if(!ctype_alnum((string)$route[$i]))
					{
						throw new Exception('Invalid routing parameters',Staple_Error::APPLICATION_ERROR);
					}
				}
				else 
				{
					$route[$i] = urlencode($route[$i]);
				}
			}
			if($route[0] == 'index')
			{
				if($routesize == 1)
				{
					return $base.$getString;
				}
				else
				{
					return $base.implode('/',$route).$getString;
				}
			}
			else 
			{
				if($route[1] == 'index')
				{
					if($routesize > 2)
					{
						if(is_numeric($route[2]))
						{
							unset($route[1]);
						}
					}
					elseif($routesize == 2)
					{
						unset($route[1]);
					}
				}
				return $base.implode('/',$route).$getString;
			}
		}
		return '#';
	}
	
	/**
	 * 
	 * Returns a urlencoded link relative to the public base of the website.
	 * @param string $link
	 */
	public function baseLink($link,$get = NULL)
	{
		return htmlentities($this->settings['application']['public_location'].$link.$get);
	}
	
	/**
	 * 
	 * This function creates an internal redirection within the script itself. It accepts the
	 * redirect as a routing string. This can be generated using the above link() function.
	 * 
	 * @link Staple_Main::link()
	 * 
	 * @param string $newRoute
	 */
	public function redirect($newRoute)
	{
		//ob_clean();
		//$this->processHeader();
		$this->route($newRoute);
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
}