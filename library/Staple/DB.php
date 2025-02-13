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

use Exception;
use mysqli;
use mysqli_result;
use SplObjectStorage;
use SplObserver;
use SplSubject;
use Staple\Exception\ConfigurationException;

class DB extends mysqli implements SplSubject
{
	use Traits\Singleton;
	
    /**
     * The object observers
     * @var SplObjectStorage
     */
    private SplObjectStorage $_observers;
    
	/**
	 * 
	 * Hostname for the database server
	 * @var string
	 */
	protected string $host;
	/**
	 * 
	 * Username to access to the database server
	 * @var string
	 */
	protected string $username;
	/**
	 * 
	 * Password to connect to the database
	 * @var string
	 */
	protected string $password;
	
	/**
	 * 
	 * Database name on the server
	 * @var string
	 */
	protected string $db;
	
	/**
	 * A boolean value that signifies an active connection to the database server.
	 * @var boolean
	 */
	protected bool $connected = false;
	
	/**
	 * Stores the last executed SQL Statement
	 * @var string
	 */
	public string $last_query;
	
	/**
	 * Storage for Named Database Connections
	 * @var array[Staple_DB]
	 */
	protected static array $namedConnections = [];
	
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
     * @return DB
     * @static
     */
	public static function get()
	{
		return static::getInstance();
	}

    /**
     * Creates and/or returns a named database connection.
     * @param string $name
     * @return DB
     * @throws ConfigurationException
     * @static
     */
	public static function getNamedConnection(string $name): DB
    {
		if (!isset(self::$namedConnections[$name])) {
			$c = __CLASS__;
			self::$namedConnections[$name] = new $c(Config::get($name));
		}
		return self::$namedConnections[$name];
	}

    /**
     * Establishes a connection to a MySQL database using the provided parameters.
     *
     * @param string|null $hostname The hostname of the database server.
     * @param string|null $username The username for the database connection.
     * @param string|null $password The password for the database connection.
     * @param string|null $database The name of the database to connect to.
     * @param int|null $port The port number for the database connection.
     * @param string|null $socket The socket or named pipe to use for the connection.
     * @return bool Returns true if the connection is successful.
     * @throws Exception If database connection parameters are not specified.
     */
    public function connect(?string $hostname = null, ?string $username = null, ?string $password = null, ?string $database = null, ?int $port = null, ?string $socket = null): bool
    {
		if($this->isReady())
		{
            try {
                $this->__construct();
                return true;
            } catch (Exception $e) {
                throw new Exception("Database Connection Error", 0, $e);
            }
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
	public function change_user(string $username, string $password, ?string $database): bool
    {
		if(isset($database))
		{
			$this->setDb($database);
		}
		$this->setUsername($username);
		$this->setPassword($password);
		return parent::change_user($this->getUsername(), $this->password, $this->getDb());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see mysqli::select_db()
	 */
	public function select_db(string $database): bool
    {
		$this->setDb($database);
		return parent::select_db($this->getDb());
	}

    /**
     * Runs a query against the database
     *
     * @param string $query
     * @param int $result_mode
     * @return mysqli_result | bool
     * @throws Exception
     */
	public function query(string $query = '', int $result_mode = MYSQLI_STORE_RESULT): mysqli_result|bool
    {
		/**
		 * @todo add self::multi_query to this function.
		 */
		if($this->connected === true)
		{
			$this->last_query = $query;
			return parent::query($query, $result_mode);
		}
		else
		{
			throw new Exception('No Database Connection');
		}
	}
	
	/**
	 * @return string $host
	 */
	public function getHost(): string
    {
		return $this->host;
	}

    /**
     * Sets the host for the connection
     *
     * @param string $host The hostname to set
     * @return static Returns the current instance
     */
	public function setHost(string $host): static
    {
		$this->host = $host;
		return $this;
	}

    /**
     * Retrieves the username.
     *
     * @return string The username.
     */
	public function getUsername(): string
    {
		return $this->username;
	}

    /**
     * Sets the username for the current instance.
     *
     * @param string $username The username to be set.
     * @return static Returns the current instance.
     */
	public function setUsername(string $username): static
    {
		$this->username = $username;
		return $this;
	}

    /**
     * Retrieves the name of the database.
     *
     * @return string The name of the database.
     */
	public function getDb(): string
    {
		return $this->db;
	}

    /**
     * Sets the database name.
     *
     * @param string $db The name of the database to set.
     * @return static Returns the current instance for method chaining.
     */
	public function setDb(string $db): static
    {
		$this->db = $db;
		return $this;
	}

    /**
     * Sets the database password parameter.
     *
     * @param string $password
     * @return DB
     */
	public function setPassword(string $password): static
    {
		$this->password = $password;
		return $this;
	}

    /**
     * Determines the database connection status.
     *
     * @return bool Returns true if connected to the database; otherwise, false.
     */
	public function getConnected(): bool
    {
		return $this->connected;
	}

    /**
     * Sets the connection status.
     *
     * @param bool $connected Indicates whether the connection is active or not.
     * @return static
     */
	protected function setConnected(bool $connected): static
    {
		$this->connected = (bool)$connected;
		return $this;
	}

    /**
     * Retrieves the most recently executed database query.
     *
     * @return string The last executed query as a string.
     */
	public function getLastQuery(): string
    {
		return $this->last_query;
	}

	/**
	 * @param string $last_query
	 */
	protected function setLastQuery($last_query)
	{
		$this->last_query = $last_query;
	}
	
	/**
	 * Returns any database errors that occurred on the last database query.
	 * @return array[array];
	 */
	public function getErrors()
	{
		return $this->error_list;
	}

	/**
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