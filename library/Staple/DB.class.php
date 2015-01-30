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
 * @deprecated
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

use \mysqli, \Exception, \SplObserver, \SplSubject, \SplObjectStorage;

class DB extends mysqli implements SplSubject
{
	use Traits\Singleton;
	
    /**
     * The object observers
     * @var SplObjectStorage
     */
    private $_observers;
    
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
	 * Stores the last executed SQL Statement
	 * @var string
	 */
	public static $last_query;
	
	/**
	 * Storage for Named Database Connections
	 * @var array[Staple_DB]
	 */
	protected static $namedConnections = array();
	
	/**
	 * 
	 * Overrides the default MySQLi constructor to add funtionality to retrieve database 
	 * settings from configuration file.
	 * 
	 * @throws Exception
	 */
	public function __construct(array $config = array())
	{
	    //Setup Object Storage for observers
	    $this->_observers = new SplObjectStorage();
	    
		if($this->checkConfig($config))
		{
			$this->host = $config['host'];
			$this->username = $config['username'];
			$this->password = $config['password'];
			$this->db = $config['db'];
		}
		elseif(!$this->isReady())
		{
			$globalSettings = Config::get('db');
			if($this->checkConfig($globalSettings))
			{
				$this->host = $globalSettings['host'];
				$this->username = $globalSettings['username'];
				$this->password = $globalSettings['password'];
				$this->db = $globalSettings['db'];
			}
		}
		
		if($this->isReady())
		{
			@parent::__construct($this->host, $this->username, $this->password, $this->db);
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
	
	/**
	 * Upon destruction attempt to close the database connection.
	 */
	public function __destruct()
	{
		//Close an open connection
		try{
			@$this->close();
		}
		catch(Exception $e){}
	}
	
    /**
     * Creates and returns the primary database connection.
     * @return Staple_DB
     * @static
     */
	public static function get(array $conf = array())
	{
		return static::getInstance();
	}
	
	/**
	 * Creates and/or returns a named database connection.
	 * @return Staple_DB
	 * @static
	 */
	public static function getNamedConnection($name)
	{
		if (!isset(self::$namedConnections[$name])) {
			$c = __CLASS__;
			self::$namedConnections[$name] = new $c(Config::get($name));
		}
		return self::$namedConnections[$name];
	}
	
	/**
	 * Overrides the MySQL connect() function to perform a check for required connection details.
	 * connect() is an alias for mysqli->__construct().
	 * @see mysqli::connect()
	 */
	public function connect($host = NULL, $user = NULL, $password = NULL, $database = NULL, $port = NULL, $socket = NULL)
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
	
	/**
	 * (non-PHPdoc)
	 * @see mysqli::change_user()
	 * @return bool
	 */
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
	
	/**
	 * (non-PHPdoc)
	 * @see mysqli::select_db()
	 */
	public function select_db($dbname)
	{
		$this->setDb($dbname);
		return parent::select_db($this->getDb());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see mysqli::query()
	 * @return mysqli_result | bool
	 */
	public function query($query,$resultmode = MYSQLI_STORE_RESULT)
	{
		/**
		 * @todo add self::multi_query to this function.
		 */
		if($this->connected === true)
		{
			self::$last_query = $query;
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
	 * @return the $connected
	 */
	public function getConnected()
	{
		return $this->connected;
	}

	/**
	 * @param boolean $connected
	 */
	protected function setConnected($connected)
	{
		$this->connected = (bool)$connected;
		return $this;
	}

	/**
	 * @return the $last_query
	 */
	public function getLastQuery()
	{
		return self::$last_query;
	}

	/**
	 * @param string $last_query
	 */
	protected function setLastQuery($last_query)
	{
		self::$last_query = $last_query;
	}
	
	/**
	 * Returns any database errors that occurred on the last database query.
	 * @return array[array];
	 */
	public function getErrors()
	{
		//PHP_VERSION 5.4.0 supports the error list array. 
		if(PHP_VERSION_ID > 50400)
		{
			return $this->error_list;
		}
		else
		{
			//Make the PHP 5.4 version manually.
			if(strlen($this->error) >= 1)
			{
				return array(array('errno'=>$this->errno, 'sqlstate'=>$this->sqlstate, 'error'=>$this->error));
			}
			else 
			{
				return array();
			}
		}
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
	
	/**
	 * Checks that all required connection parameters have been set.
	 * @return bool
	 */
	protected function isReady()
	{
		$keys = array($this->host,$this->username,$this->password,$this->db);
		foreach($keys as $config)
		{
			if(strlen($config) < 1)
			{
				return false;
			}
		}
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see SplSubject::attach()
	 */
	public function attach(SplObserver $observer)
	{
		$this->_observers->attach($observer);
	}

	/* (non-PHPdoc)
	 * @see SplSubject::detach()
	 */
	public function detach(SplObserver $observer)
	{
		$this->_observers->detach($observer);
	}

	/* (non-PHPdoc)
	 * @see SplSubject::notify()
	 */
	public function notify()
	{
		foreach($this->_observers as $observer)
		{
		    $observer->update($this);
		}
	}

}