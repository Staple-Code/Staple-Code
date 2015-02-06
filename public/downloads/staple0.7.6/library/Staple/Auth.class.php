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
class Staple_Auth
{
	/**
	 * 
	 * Holds the singleton instance for the Auth class
	 * @var Staple_Auth
	 */
	private static $instance;
	
	/**
	 * 
	 * Holds the user identifier.
	 * @var int | string
	 */
	private $userid = NULL;
	
	/**
	 * 
	 * Holds a reference to the AuthAdapter object.
	 * @var Staple_AuthAdapter
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
			throw new Exception('Authentication Module Failure', Staple_Error::AUTH_ERROR);
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
				throw new Exception('Authentication Module Configuration Error',Staple_Error::AUTH_ERROR);
			}
		}
		return true;
	}
	
	/**
	 * 
	 * Gets the singleton instance of the object. Checks the session to see if a current auth
	 * object already exists. If not a new Auth object is created.
	 * @return Staple_Auth
	 */
	public static function get()
	{			
		if(!(self::$instance instanceof Staple_Auth))
		{
			if(array_key_exists('Staple', $_SESSION))
				if(array_key_exists('auth', $_SESSION['Staple']))
					self::$instance = $_SESSION['Staple']['auth'];
			if(!(self::$instance instanceof Staple_Auth))
				self::$instance = new Staple_Auth();
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
		return $this->userid;
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
		if(!($this->adapter instanceof Staple_AuthAdapter))
		{
			$adapt = $this->_settings['adapter'];
			$this->adapter = new $adapt();
			if(!($this->adapter instanceof Staple_AuthAdapter))
			{
				throw new Exception('Invalid Authentication Adapter', Staple_Error::AUTH_ERROR);
			}
		}
		
		//Check Auth against the adapter
		if($this->adapter->getAuth($credentials) === true)
		{
			session_regenerate_id();
			$this->authed = true;
			$this->userid = $this->adapter->getUserId();
			$this->authLevel = $this->adapter->getLevel($this->userid);
			$this->message = "Authentication Successful";
			return true;
		}
		else
		{
			$this->authed = false;
			$this->userid = null;
			$this->authLevel = 0;
			$this->message = "Authentication Failed";
		}
		return false;
	}
	
	/**
	 * In the event that authorization fails, this method is called by the framework. noAuth() 
	 * dispatches to the AuthController -> index action.
	 */
	public function noAuth()
	{
		$this->dispatchAuthController();
	}
	
	/**
	 * 
	 * General log out or clear credentials function.
	 */
	public function clearAuth()
	{
		$this->userid = NULL;
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
	 * @throws Exception
	 */
	private function dispatchAuthController()
	{
		$conString = $this->_settings['controller'];
		$class = substr($conString, 0, strlen($conString)-10);
		$authCon = Staple_Main::getController($class);
		if(!($authCon instanceof Staple_AuthController))
		{
			$authCon = new $conString();
		}
		if($authCon instanceof Staple_AuthController)
		{
			//Start the Controller
			$authCon->_start();
			
			//Register Auth Controller with the Front Controller
			Staple_Main::get()->registerController($authCon);
			
			//Set the view's controller to match the route
			$authCon->view->setController($class);
			
			//Set the view's action to match the route
			$authCon->view->setView('index');
			
			//Call the controller action, Send the route requested to the action
			//@todo Add option to customize the controller action
			call_user_func_array(array($authCon,'index'), array(Staple_Main::getRoute()));
			
			//Grab the buffer contents from the controller and post it after the header.
			$buffer = ob_get_contents();
			ob_clean();
			
			//Process the header
			Staple_Main::get()->processHeader();
			
			if($authCon->layout instanceof Staple_Layout)
			{
				$authCon->layout->build($buffer);
			}
			else
			{
				echo $buffer;
				$authCon->view->build();
			}
		}
		else
		{
			throw new Exception('Fatal Error connecting to Auth Controller', Staple_Error::AUTH_ERROR);
		}
	}
}