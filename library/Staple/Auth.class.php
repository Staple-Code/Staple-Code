<?PHP
/**
 * 
 * This class is the central class for site-wide authentication.
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
 *
 */
namespace Staple;

use \Exception;
use \ReflectionMethod;
use \ReflectionClass;
use Staple\Exception\PageNotFoundException;
use Staple\Exception\AuthException;

class Auth
{
	/**
	 * 
	 * Holds the singleton instance for the Auth class
	 * @var Auth
	 */
	private static $instance;
	
	/**
	 * 
	 * Holds the user identifier.
	 * @var int | string
	 */
	private $userId = NULL;
	
	/**
	 * 
	 * Holds a reference to the AuthAdapter object.
	 * @var AuthAdapter
	 */
	private $adapter;
	
	/**
	 * 
	 * Holds the configuration for this object.
	 * @var array
	 */
	private $_settings = array();
	
	/**
	 * 
	 * Private variable holds the Auth status.
	 * @var bool
	 */
	private $authed = false;
	/**
	 * 
	 * The level of authorization as set by the AuthAdapter for this session.
	 * @var int
	 */
	private $authLevel = 0;
	
	/**
	 * 
	 * Status message from the Auth class.
	 * @var string
	 */
	protected $message = '';
	
	/**
	 * 
	 * The private constructor ensures that the object is created as a Singleton. On initialization
	 * the class checks for a configuration file. If none is found it throws an Exception. If a
	 * config file is found, it parses the file and checks for the config keys required by this
	 * class.
	 * 
	 * @throws Exception
	 */
	private function __construct()
	{
		$this->authed = false;
		if(file_exists(CONFIG_ROOT.'auth.ini'))
		{
			$curConfig = parse_ini_file(CONFIG_ROOT.'auth.ini');
			if($this->checkConfig($curConfig))
			{
				$this->_settings = $curConfig;
			}
		}
		elseif(file_exists(CONFIG_ROOT.'application.ini'))
		{
			$curConfig = parse_ini_file(CONFIG_ROOT.'application.ini',true);
			if($this->checkConfig($curConfig['auth']))
			{
				$this->_settings = $curConfig['auth'];
			}
		}
		else
		{
			throw new Exception('Authentication Module Failure', Error::AUTH_ERROR);
		}
	}
	
	/**
	 * The destructor store the auth instance in the session.
	 */
	public function __destruct()
	{
		$_SESSION['Staple']['auth'] = self::$instance;
	}
	
	/**
	 * 
	 * Accepts the parsed configuration file and checks for configuration keys required by the
	 * class. If a key is missing, it throws an Exception cancelling the execution of the script.
	 * 
	 * @param array $conf
	 * @throws Exception
	 * @return bool
	 */
	private function checkConfig(array $conf)
	{
		$keys = array('enabled','adapter','controller');
		foreach($keys as $keyval)
		{
			if(!array_key_exists($keyval, $conf))
			{
				throw new Exception('Authentication Module Configuration Error',Error::AUTH_ERROR);
			}
		}
		return true;
	}
	
	/**
	 * 
	 * Gets the singleton instance of the object. Checks the session to see if a current auth
	 * object already exists. If not a new Auth object is created.
	 * @return Auth
	 */
	public static function get()
	{			
		if(!(self::$instance instanceof Auth))
		{
			if(array_key_exists('Staple', $_SESSION))
				if(array_key_exists('auth', $_SESSION['Staple']))
					self::$instance = $_SESSION['Staple']['auth'];
			if(!(self::$instance instanceof Auth))
				self::$instance = new Auth();
		}
		return self::$instance;
	}
	
	/**
	 * 
	 * Returns a boolean representing authorization status. True for any level of authorization,
	 * false for no authorization.
	 * @return bool
	 */
	public function isAuthed()
	{
		return $this->authed;
	}
	
	/**
	 * Returns and integer representing the level of access. Defaults to 0 for no auth and 1 
	 * for general authorization. This is derived from information gathered by the AuthAdapter.
	 * @return int
	 */
	public function getAuthLevel()
	{
		return (int)$this->authLevel;
	}
	
	/**
	 * 
	 * Returns the Auth ID
	 * @return int | string
	 */
	public function getAuthId()
	{
		return $this->userId;
	}
	
	/**
	 * 
	 * Attempts authorization, accepting credentials and forwarding them to the AuthAdapter.
	 * Throws and Exception if the AuthAdapter is not implemented from Staple_AuthAdapter. 
	 * Returns a boolean to signify if authorization succeeded.
	 * @param array $credentials
	 * @throws Exception
	 * @return bool
	 */
	public function doAuth(array $credentials)
	{
		//Make sure an adapter is loaded.
		if(!($this->adapter instanceof AuthAdapter))
		{
			$adapt = $this->_settings['adapter'];
			$this->adapter = new $adapt();
			if(!($this->adapter instanceof AuthAdapter))
			{
				throw new Exception('Invalid Authentication Adapter', Error::AUTH_ERROR);
			}
		}
		
		//Check Auth against the adapter
		if($this->adapter->getAuth($credentials) === true)
		{
			session_regenerate_id();
			$this->authed = true;
			$this->userId = $this->adapter->getUserId();
			$this->authLevel = $this->adapter->getLevel($this->userId);
			$this->message = "Authentication Successful";
			return true;
		}
		else
		{
			$this->authed = false;
			$this->userId = null;
			$this->authLevel = 0;
			$this->message = "Authentication Failed";
		}
		return false;
	}
	
	/**
	 * In the event that authorization fails, this method is called by the framework. noAuth() 
	 * dispatches to the AuthController -> index action.
	 * This method accepts an optional route parameter that can be sent forward to the auth controller
	 * which will allow the developer to react to the route that was requested.
	 * @param Route $attemptedRoute
	 */
	public function noAuth($attemptedRoute = NULL)
	{
		$this->dispatchAuthController($attemptedRoute);
	}
	
	/**
	 * 
	 * General log out or clear credentials function.
	 */
	public function clearAuth()
	{
		$this->userId = NULL;
		$this->authed = false;
		$this->authLevel = 0;
		$this->message = 'Logged Out';
	}
	
	/**
	 * 
	 * Returns the Auth message from the class
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * 
	 * Dispatches to the AuthController -> index action. Throws an Exception if the controller does
	 * not extend Staple_AuthController.
	 * @param Route $previousRoute
	 * @throws Exception
	 */
	private function dispatchAuthController($previousRoute = NULL)
	{
		//Get and Construct the Auth Controller
		$controllerClass = Config::getValue('auth','controller'); //$this->_settings['controller'];
		$class = substr($controllerClass, 0, strlen($controllerClass)-10);

		//Setup the action to call
		$action = (strlen(Config::getValue('auth', 'action', false)) > 0) ? Config::getValue('auth', 'action', false) : 'index';

		//Check for the controller existence
		if(class_exists($controllerClass))
		{
			//Check for the action existence
			if (method_exists($controllerClass, $action))
			{
				//Create and Start the Auth Controller
				$controller = new $controllerClass();
				$controller->_start();

				//Register the controller with the front controller
				Main::controller($controllerClass);

				//If the controller
				//@todo Add support for AuthProviders here as well
				if ($controller instanceof AuthController)
				{
					//Set the view's controller to match the route
					$controller->view->setController($class);

					//Set the view's action to match the route
					$controller->view->setView($action);

					//Call the controller action
					$actionMethod = new ReflectionMethod($controller, $action);
					$return = $actionMethod->invokeArgs($controller, array($previousRoute));

					if ($return instanceof View)        //Check for a returned View object
					{
						//If the view does not have a controller name set, set it to the currently executing controller.
						if ($return->hasController() == false)
						{
							$loader = Main::get()->getLoader();
							$conString = get_class($controller);

							$return->setController(substr($conString, 0, strlen($conString) - strlen($loader::CONTROLLER_SUFFIX)));
						}

						//Check for a controller layout and build it.
						if ($controller->layout instanceof Layout)
						{
							$controller->layout->build(NULL, $return);
						}
						else
						{
							$return->build();
						}

						//The view has been built return true
						return true;
					}
					elseif ($return instanceof Json)    //Check for a Json object to be coverted and echoed.
					{
						echo json_encode($return);

						//JSON echoed return true
						return true;
					}
					elseif (is_object($return))        //Check for another object type
					{
						//If the object is stringable, covert it to a string and output it.
						$class = new ReflectionClass($return);
						if ($class->implementsInterface('JsonSerializable'))
						{
							echo json_encode($return);

							//Object successfully converted to JSON
							return true;
						}
						//If the object is stringable, covert to a string and output it.
						elseif ((!is_array($return)) &&
							((!is_object($return) && settype($return, 'string') !== false) ||
								(is_object($return) && method_exists($return, '__toString'))))
						{
							echo (string)$return;

							//Object stringified successfully
							return true;
						}
						//If nothing else works, echo the object through the dump method.
						else
						{
							Dev::Dump($return);

							//Object was dumped to the browser
							return true;
						}
					}
					elseif (is_string($return))        //If the return value was simply a string, echo it out.
					{
						echo $return;

						//String sent to the browser
						return true;
					}
					else
					{
						//Fall back to previous functionality by rendering views and layouts.
						if ($controller->layout instanceof Layout)
						{
							$controller->layout->build();
						}
						else
						{
							$controller->view->build();
						}

						//The legacy view has been built return true
						return true;
					}
				}
				else
				{
					throw new AuthException('Fatal Error connecting to Auth Controller', Error::AUTH_ERROR);
				}
			}
		}

		//Throw an exception if the auth page cannot be found
		throw new PageNotFoundException('Page Not Found.', Error::PAGE_NOT_FOUND);
	}
}