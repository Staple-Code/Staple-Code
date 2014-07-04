<?PHP
/**
 * The Staple_DB class extends the MySQLi class to give added functionality.  The Database
 * configuration file requires these fields:
 * 
 * host - the mysql hostname of the database server to connect to.
 * username - MySQL username to use to connect to the database.
 * password - MySQL password to use to connect to the database.
 * db - the database to bind to.
 * 
 * Database configuration file is optional. You can specify a configuration array and send it to 
 * the get function at runtime, or you can use the set functions to configure the database 
 * connection. If you use the set functions, you will have to manually connect to the database 
 * by calling the connect function or constructor for this object.
 * 
 * @todo add a database reconnection function for when the username, host, pw, or db changes.
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
class Staple_DB extends mysqli
{
	/**
	 * 
	 * Holds the singleton instance for the object.
	 * @var Staple_DB
	 * @static
	 */
	protected static $conn;
	/**
	 * 
	 * Hostname for the database server
	 * @var string
	 */
	protected $host;
	/**
	 * 
	 * Username to access to the database server
	 * @var string
	 */
	protected $username;
	/**
	 * 
	 * Password to connect to the database
	 * @var string
	 */
	protected $password;
	
	/**
	 * 
	 * Database name on the server
	 * @var string
	 */
	protected $db;
	
	/**
	 * A boolean value that signifies an active connection to the database server.
	 * @var boolean
	 */
	protected $connected = false;
	
	/**
	 * 
	 * Overrides the default MySQLi constructor to add funtionality to retrieve database 
	 * settings from configuration file.
	 * 
	 * @throws Exception
	 */
	protected function __construct(array $config = array())
	{
		if($this->checkConfig($config))
		{
			$this->host = $config['host'];
			$this->username = $config['username'];
			$this->password = $config['password'];
			$this->db = $config['db'];
		}
		elseif(defined('CONFIG_ROOT'))
		{
			$settings = array();
			if(file_exists(CONFIG_ROOT.'application.ini'))
			{
				$curConfig = parse_ini_file(CONFIG_ROOT.'application.ini',true);
				if($this->checkConfig($curConfig['db']))
				{
					$settings = $curConfig['db'];
					$this->host = $settings['host'];
					$this->username = $settings['username'];
					$this->password = $settings['password'];
					$this->db = $settings['db'];
				}
			}
		}
		
		if($this->isReady())
		{
			parent::__construct($this->host, $this->username, $this->password, $this->db);
			if(isset($this->connect_error))
			{
				throw new Exception("Database Connection Error");
			}
			else
			{
				$this->connected = true;
			}
		}
	}
	
	public function __destruct()
	{
		@$this->close();
	}
	
	public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
	
    /**
     * 
     * Creates a singlton instance of the Staple_DB object, extending from MySQLi extension.
     * @return Staple_DB
     * @static
     */
	public static function get(array $conf = array())
	{
		if (!isset(self::$conn) || $conf != array()) {
            $c = __CLASS__;
            self::$conn = new $c($conf);
        }
		return self::$conn;
	}
	
	public function connect()
	{
		if($this->isReady())
		{
			$this->__construct();
		}
		else
		{
			throw new Exception("Database Connection Parameters Not Specified.");
		}
	}
	
	public function change_user($user, $password, $database = NULL)
	{
		if(isset($database))
		{
			$this->setDb($database);
		}
		$this->setUsername($user);
		$this->setPassword($password);
		return parent::change_user($this->getUsername(), $this->password, $this->getDb());
	}
	
	public function select_db($dbname)
	{
		$this->setDb($dbname);
		return parent::select_db($this->getDb());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see mysqli::query()
	 * @return mysqli_result | true | false
	 */
	public function query($query,$resultmode = MYSQLI_STORE_RESULT)
	{
		/**
		 * @todo add self::multi_query to this function.
		 */
		if($this->connected === true)
		{
			return parent::query($query,$resultmode);
		}
		else
		{
			throw new Exception('No Database Connection');
		}
	}
	
	/**
	 * @return the $host
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @param string $host
	 */
	public function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	/**
	 * @return the $username
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	/**
	 * @return the $db
	 */
	public function getDb()
	{
		return $this->db;
	}

	/**
	 * @param string $db
	 */
	public function setDb($db)
	{
		$this->db = $db;
		return $this;
	}

	/**
	 * Sets the database password parameter.
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}
	
	/**
	 * 
	 * Checks the configuration file to make sure that all required keys exist.
	 * @param array $config
	 * @throws Exception
	 */
	protected function checkConfig(array $config)
	{
		$keys = array('host','username','password','db');
		foreach($keys as $value)
		{
			if(!array_key_exists($value, $config))
			{
				return false;
			}
		}
		return true;
	}
	
	protected function isReady()
	{
		$keys = array($this->host,$this->username,$this->password,$this->db);
		foreach($keys as $config)
		{
			if(strlen($config) < 2)
			{
				return false;
			}
		}
		return true;
	}
}