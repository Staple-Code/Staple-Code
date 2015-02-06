<?PHP
/**
 * The Staple_DB class extends the MySQLi class to give added functionality. Database
 * configuration file requires these fields:
 * 
 * host - the mysql hostname of the database server to connect to.
 * username - MySQL username to use to connect to the database.
 * password - MySQL password to use to connect to the database.
 * db - the database to bind to.
 * 
 * @todo add a database reconnection function for when the username, host, pw, or db changes.
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
	 * 
	 * Overrides the default MySQLi constructor to add funtionality to retrieve database 
	 * settings from configuration file.
	 * 
	 * @throws Exception
	 */
	protected function __construct()
	{
		$settings = array();
		$dbConfig = CONFIG_ROOT.'db.ini';
		if(file_exists($dbConfig))
		{
			$curConfig = parse_ini_file(CONFIG_ROOT.'db.ini');
			if($this->checkConfig($curConfig))
			{
				$settings = $curConfig;
			}
		}
		elseif(file_exists(CONFIG_ROOT.'application.ini'))
		{
			$curConfig = parse_ini_file(CONFIG_ROOT.'application.ini',true);
			if($this->checkConfig($curConfig['db']))
			{
				$settings = $curConfig['db'];
			}
		}
		else
			throw new Exception('Database Configuration Not Found');
			
		$this->host = $settings['host'];
		$this->username = $settings['username'];
		$this->password = $settings['password'];
		$this->db = $settings['db'];
		
		parent::__construct($this->host, $this->username, $this->password, $this->db);
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
	public static function get()
	{
		if (!isset(self::$conn)) {
            $c = __CLASS__;
            self::$conn = new $c();
        }
		return self::$conn;
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
				throw new Exception('Staple_DB configuration error.',Staple_Error::DB_ERROR);
			}
		}
		return true;
	}
}