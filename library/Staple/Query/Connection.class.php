<?php
/**
 * An extension of the PDO classes to supply a database connection to the framework.
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

namespace Staple\Query;

use PDO;
use PDOStatement;
use SplObjectStorage;
use SplObserver;
use Staple\Config;

class Connection extends PDO implements IConnection
{
	const DRIVER_SQLSRV = 'sqlsrv';
	const DRIVER_MYSQL = 'mysql';
	const DRIVER_SQLITE = 'sqlite';
	const DRIVER_PGSQL = 'pgsql';
	const DRIVER_MSSQL = 'mssql';
	const DRIVER_DBLIB = 'dblib';
	const DRIVER_SYBASE = 'sybase';
	const DRIVER_ODBC = 'odbc';
	const DRIVER_OCI = 'oci';
	const DRIVER_CUBRID = 'cubrid';
	const DRIVER_4D = '4D';
	const DRIVER_INFORMIX = 'informix';
	const DRIVER_IBM = 'ibm';
	const DRIVER_FIREBIRD = 'firebird';

	/**
	 * The object observers. Used to catch and handle or log errors with the database and queries.
	 * @var SplObjectStorage
	 */
	protected static $_observers;

	/**
	 * The database driver that is being used
	 * @var string
	 */
	protected $driver;
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
	 * Array of connector options
	 * @var array
	 */
	protected $options = array();

	/**
	 * Stores log of the previously executed queries
	 * @var string
	 */
	protected $queryLog = array();

	/**
	 * Stores the last executed SQL Statement
	 * @var string
	 */
	public $lastQuery;

	/**
	 * Array of named connections that the application can retrieve.
	 * @var array
	 */
	protected static $namedConnections = array();

	/**
	 * @param $dsn
	 * @param string $username
	 * @param string $password
	 * @param array $options
	 */
	public function __construct($dsn, $username = NULL, $password = NULL, array $options = array())
	{
		self::$_observers = new SplObjectStorage();

		parent::__construct($dsn,$username,$password,$options);

		if(!isset($this->driver)) $this->setDriver(self::getDriverFromDsn($dsn));

		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS,array('\Staple\Query\Statement'));

		if(isset($username))
			$this->setUsername($username);
		if(isset($password))
			$this->setPassword($password);

		//Set the options property
		$this->setOptions($options);
	}

	/**
	 * Convert old mysqli properties to PDO method calls.
	 * @deprecated
	 * @param $name
	 * @return null|string
	 */
	public function __get($name)
	{
		switch($name)
		{
			case 'insert_id':
				return $this->lastInsertId();
				break;
			case 'last_query':
				return $this->getLastQuery();
				break;
			case 'error':
				return $this->errorInfo();
				break;
			case 'errno':
				return $this->errorCode();
				break;
			default:
				return NULL;
		}
	}

	/**
	 * This is provided as backward compatibility with previous MySQLi driver
	 * @deprecated
	 * @param $string
	 * @return string
	 */
	public function real_escape_string($string)
	{
		return $this->quote($string);
	}

	/**
	 * This is provided as backward compatibility with previous MySQLi driver
	 * @deprecated
	 * @param $string
	 * @return string
	 */
	public function escape_string($string)
	{
		return $this->quote($string);
	}

	/**
	 * @param array $config
	 * @return static
	 */
	protected static function createFromConfig(array $config)
	{
		if(array_key_exists('dsn',$config))
		{
			//Use a defined DSN from the config
			$dsn = $config['dsn'];
		}
		else
		{
			//Build the DSN from the config input
			$dsn = self::buildDsnFromConfig($config);
		}

		//Look for connection options from the configuration
		if(isset($config['options']))
		{
			if(is_array($config['options']))
			{
				$options = $config['options'];
			}
			else
			{
				$options = array();
			}
		}
		else
		{
			$options = array();
		}

		//Check for SSL Params - MySQL Specific
		if($config['driver'] == self::DRIVER_MYSQL)
		{
			//SSL Certificate
			if(isset($config['ssl_cert']))
				$options[PDO::MYSQL_ATTR_SSL_CERT] = $config['ssl_cert'];

			//SSL Private Key
			if(isset($config['ssl_key']))
				$options[PDO::MYSQL_ATTR_SSL_KEY] = $config['ssl_key'];

			//SSL CA
			if(isset($config['ssl_ca']))
				$options[PDO::MYSQL_ATTR_SSL_CA] = $config['ssl_ca'];
		}

		//Call the constructor.
		$inst = new static($dsn, $config['username'], $config['password'], $options);

		//Set the driver to use
		isset($config['driver']) ? $inst->setDriver($config['driver']) : $inst->setDriver(self::DRIVER_MYSQL);

		//Register the options used
		$inst->setOptions($options);

		//Set the DB Name property
		if(isset($config['db']))
			$inst->setDb($config['db']);

		return $inst;
	}

	protected static function buildDsnFromConfig(array $config)
	{
		//Initialize the DSN var
		$dsn = '';

		//Check for a valid driver, default to mysql by default
		if(isset($config['driver']))
		{
			//Build the DSN based on the driver type supplied
			switch ($config['driver'])
			{
				case self::DRIVER_SQLSRV:
					$dsn .= self::DRIVER_SQLSRV . ':';
					$dsn .= 'Server=' . $config['host'] . ';';
					$dsn .= 'Database=' . $config['db'];
					break;
				case self::DRIVER_MYSQL:
					$dsn .= self::DRIVER_MYSQL . ':';
					$dsn .= 'host=' . $config['host'] . ';';
					$dsn .= 'dbname=' . $config['db'];
					break;
				case self::DRIVER_SQLITE:
				case self::DRIVER_PGSQL:
				case self::DRIVER_MSSQL:
				case self::DRIVER_DBLIB:
				case self::DRIVER_SYBASE:
				case self::DRIVER_ODBC:
				case self::DRIVER_OCI:
				case self::DRIVER_CUBRID:
				case self::DRIVER_4D:
				case self::DRIVER_INFORMIX:
				case self::DRIVER_IBM:
				case self::DRIVER_FIREBIRD:
					$dsn .= $config['driver'] . ':';
					break;
				default:
					$dsn .= self::DRIVER_MYSQL . ':';
					$dsn .= 'host=' . $config['host'] . ';';
					$dsn .= 'dbname=' . $config['db'];
			}
		}
		else
		{
			$dsn .= self::DRIVER_MYSQL . ':';
			$dsn .= 'host=' . $config['host'] . ';';
			$dsn .= 'dbname=' . $config['db'];
		}

		return $dsn;
	}
	
	protected function getDriverFromDsn($dsn)
	{
		$dsnString = explode(':',$dsn);
		$driverList = $this->getAvailableDrivers();
		if(($driverKey = array_search($dsnString[0],$driverList)) !== false)
		{
			return $driverList[$driverKey];
		}
		return NULL;
	}

	/**
	 * Get the default database instance.
	 * @return $this
	 */
	public static function getInstance()
	{
		if (!isset(static::$namedConnections['__DEFAULT__']))
		{
			static::$namedConnections['__DEFAULT__'] = static::createFromConfig(Config::get('db'));
		}
		return static::$namedConnections['__DEFAULT__'];
	}

	/**
	 * This is an alias of the getInstance() method.
	 * @alias getInstance()
	 */
	public static function get()
	{
		return static::getInstance();
	}

	/**
	 * Create or return a named connection
	 * @param $namedInstance
	 * @return mixed
	 */
	public static function getNamedConnection($namedInstance)
	{
		if (!isset(self::$namedConnections[$namedInstance]))
		{
			/** @var Connection $c */
			$c = __CLASS__;
			self::$namedConnections[$namedInstance] = $c::createFromConfig(Config::get($namedInstance));
		}
		return self::$namedConnections[$namedInstance];
	}

	/*-------------------------------------------------Getters and Setters-------------------------------------------------*/

	/**
	 * @return string
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @param string $driver
	 * @return $this
	 */
	public function setDriver($driver)
	{
		switch($driver)
		{
			case self::DRIVER_4D:
			case self::DRIVER_CUBRID:
			case self::DRIVER_DBLIB:
			case self::DRIVER_FIREBIRD:
			case self::DRIVER_IBM:
			case self::DRIVER_INFORMIX:
			case self::DRIVER_MSSQL:
			case self::DRIVER_MYSQL:
			case self::DRIVER_OCI:
			case self::DRIVER_ODBC:
			case self::DRIVER_PGSQL:
			case self::DRIVER_SQLITE:
			case self::DRIVER_SQLSRV:
			case self::DRIVER_SYBASE:
				$this->driver = (string)$driver;
				break;
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @param string $host
	 * @return $this
	 */
	public function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 * @return $this
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	/**
	 * @param string $password
	 * @return $this
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDb()
	{
		return $this->db;
	}

	/**
	 * @param string $db
	 * @return $this
	 */
	public function setDb($db)
	{
		$this->db = $db;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 * @return $this
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
		return $this;
	}

	public function addOption($key,$value)
	{
		$this->options[$key] = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getQueryLog()
	{
		return $this->queryLog;
	}

	/**
	 * @return string
	 */
	public function getLastQuery()
	{
		return $this->lastQuery;
	}

	/**
	 * @param string $lastQuery
	 * @return $this
	 */
	public function setLastQuery($lastQuery)
	{
		$this->lastQuery = $lastQuery;
		return $this;
	}

	/**
	 * Add a query to the query log and set the lastQuery property to that query.
	 * @param $query
	 * @return $this
	 */
	protected function addQueryToLog($query)
	{
		$this->queryLog[] = $query;
		$this->setLastQuery($query);
		return $this;
	}

	/**
	 * Clear the query log
	 * @return $this;
	 */
	public function clearQueryLog()
	{
		$this->queryLog = array();
		$this->lastQuery = NULL;
		return $this;
	}

	/*-------------------------------------------------Query Functions-------------------------------------------------*/

	/**
	 * Get Driver Specific Options for Prepared Statements
	 *
	 * @return array
	 */
	public function getDriverOptions()
	{
		$options = [];
		switch($this->getDriver())
		{
			case self::DRIVER_SQLSRV:
				$options[PDO::ATTR_CURSOR] = PDO::CURSOR_SCROLL;
				$options[PDO::SQLSRV_ATTR_CURSOR_SCROLL_TYPE] = PDO::SQLSRV_CURSOR_STATIC;
				break;
		}

		return $options;
	}

	/**
	 * @param string $statement
	 * @return PDOStatement | boolean
	 */
	public function exec($statement)
	{
		$this->addQueryToLog($statement);

		//Execute the query and check for errors
		if(($return = parent::exec($statement)) === false)
		{
			//Notify the observers that an error has occurred.
			$this->notify();
		}

		//Return the result
		return $return;
	}

	/**
	 * @param string $statement
	 * @return Statement
	 */
	public function query($statement)
	{
		//Log the query
		$this->addQueryToLog((string)$statement);

		//Execute the query and check for errors
		if(($result = parent::query((string)$statement, PDO::FETCH_CLASS, '\Staple\Query\Statement')) === false)
		{
			//Notify the observers that an error has occurred.
			$this->notify();
		}
        else
		{
			//Assign the driver type in the statement object
			if ($result instanceof Statement)
				$result->setDriver($this->getDriver());
		}

		//Return the result
		return $result;
	}

	/*-------------------------------------------------Observer Methods-------------------------------------------------*/

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Attach an SplObserver
	 * @link http://php.net/manual/en/splsubject.attach.php
	 * @param SplObserver $observer <p>
	 * The <b>SplObserver</b> to attach.
	 * </p>
	 * @return $this
	 */
	public function attach(SplObserver $observer)
	{
		self::$_observers->attach($observer);
		return $this;
	}

	/**
	 * A static call to attach an observer to the connection class.
	 * @param SplObserver $observer
	 * @return self
	 */
	public static function attachObserver(SplObserver $observer)
	{
		$connection = self::getInstance();
		$connection->attach($observer);
		return $connection;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Detach an observer
	 * @link http://php.net/manual/en/splsubject.detach.php
	 * @param SplObserver $observer <p>
	 * The <b>SplObserver</b> to detach.
	 * </p>
	 * @return $this
	 */
	public function detach(SplObserver $observer)
	{
		self::$_observers->detach($observer);
		return $this;
	}

	/**
	 * Static method to detach an observer from the connection class.
	 * @param SplObserver $observer
	 * @return self
	 */
	public static function detachObserver(SplObserver $observer)
	{
		$connection = self::getInstance();
		$connection->detach($observer);
		return $connection;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Notify an observer
	 * @link http://php.net/manual/en/splsubject.notify.php
	 * @return $this
	 */
	public function notify()
	{
		foreach(self::$_observers as $observer)
		{
			$observer->update($this);
		}

		return $this;
	}
}