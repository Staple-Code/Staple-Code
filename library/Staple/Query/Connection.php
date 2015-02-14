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

use \PDO, \PDOStatement, \Exception, \SplObserver, \SplSubject, \SplObjectStorage;

class Connection extends PDO implements SplSubject
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
	private static $_observers;

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
		parent::__construct($dsn,$username,$password,$options);
	}

	/**
	 * @param array $config
	 */
	protected static function createFromConfig(array $config)
	{
		if(array_key_exists('dsn',$config))
		{
			$dsn = $config['dsn'];
		}
		else
		{
			$dsn = self::buildDsnFromConfig($config);
		}
		if(isset($config['options']))
		{
			if(is_array($config['$options']))
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

		//Call the constructor.
		new static($dsn, $config['username'], $config['password'], $options);
	}

	protected static function buildDsnFromConfig(array $config)
	{
		$dsn = '';

		//Check for a valid driver, default to mysql by default
		switch($config['driver'])
		{
			case self::DRIVER_SQLSRV:
				$dsn .= self::DRIVER_SQLSRV.':';
				$dsn .= 'Server='.$config['host'].';';
				$dsn .= 'Database='.$config['db'];
				break;
			case self::DRIVER_MYSQL:
				$dsn .= self::DRIVER_SQLSRV.':';
				$dsn .= 'host='.$config['host'].';';
				$dsn .= 'dbname='.$config['db'];
				break;
			case self::DRIVER_SQLITE :
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
				$dsn .= $config['driver'].':';
				break;
			default:
				$dsn .= self::DRIVER_MYSQL.":";
		}


		return $dsn;
	}

	/**
	 * Get the default database instance.
	 * @return mixed
	 */
	public static function getInstance()
	{
		if (!isset(static::$inst))
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
	public function getNamedConnection($namedInstance)
	{
		if (!isset(self::$namedConnections[$namedInstance]))
		{
			$c = __CLASS__;
			self::$namedConnections[$namedInstance] = $c::createFromConfig(Config::get($namedInstance));
		}
		return self::$namedConnections[$namedInstance];
	}

	/*-------------------------------------------------Getters and Setters-------------------------------------------------*/

	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @param string $host
	 * @return this
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

	public function query($statement)
	{
		//Log the query
		$this->addQueryToLog($statement);

		//Execute the query and check for errors
		if(($result = parent::query($statement, PDO::FETCH_CLASS, 'Statement')) === false)
		{
			//Notify the observers that an error has occurred.
			$this->notify();
		}

		//Return the result
		return $result;
	}


	/*-------------------------------------------------Observer Functions-------------------------------------------------*/

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