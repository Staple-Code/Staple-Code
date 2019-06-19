<?php

/** 
 * A class for building database queries.
 * Right now the class only supports the MySQL database.
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
 * 
 */
namespace Staple\Query;

use DateTime;
use Exception;
use PDO;
use PDOException;
use PDOStatement;
use Staple\Error;
use Staple\Exception\ConfigurationException;
use Staple\Exception\QueryException;
use Staple\Pager;

abstract class Query implements IQuery
{
	
	/**
	 * Table to act upon.
	 * @var mixed
	 */
	public $table;
	/**
	 * The schema that the table lives in. For SQL Server.
	 * @var string
	 */
	protected $schema;
	
	/**
	 * The Connection database object. A database object is required to properly escape input.
	 * @var IConnection
	 */
	protected $connection;
	
	/**
	 * An array of Where Clauses. The clauses are additive, using the AND  conjunction.
	 * @var Condition[]
	 */
	protected $where = array();

	/**
	 * Set a flag if the query is to be parameterized.
	 * @var bool
	 */
	protected $parameterized = true;

	/**
	 * @param string $table
	 * @param IConnection $db
	 * @throws QueryException
	 */
	public function __construct($table = NULL, IConnection $db = NULL)
	{
		if($db instanceof IConnection)
		{
			$this->setConnection($db);
		}
		else
		{
			try {
				$this->setConnection(Connection::get());
			}
			catch (Exception $e)
			{
				throw new QueryException('Unable to find a database connection.', Error::DB_ERROR, $e);
			}
		}
		if(!($this->connection instanceof IConnection))
		{
			throw new QueryException('Unable to create database object', Error::DB_ERROR);
		}
		
		//Set Table
		if(isset($table))
		{
			$this->setTable($table);
		}
	}
	
	/**
	 * Execute the build function and return the result when converting to a string.
	 */
	public function __toString()
	{
		return $this->build();
	}
	
	/**
	 * @return Query|string $table
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @return IConnection $db
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Return the array of where conditions currently attached to the query.
	 * @return Condition[]
	 */
	public function getWhere()
	{
		return $this->where;
	}

	/**
	 * Get the schema string
	 * @return string
	 */
	public function getSchema()
	{
		return $this->schema;
	}

	/**
	 * Set the schema string
	 * @param string $schema
	 * @return $this
	 * @throws QueryException
	 */
	public function setSchema($schema)
	{
		//Check that we are not on MYSQL
		if(isset($this->connection))
			if($this->connection->getDriver() == Connection::DRIVER_MYSQL)
				throw new QueryException('Schema cannot be specified on a MySQL Connection');

		$this->schema = (string)$schema;
		return $this;
	}

	/**
	 * @param Query|string $table
	 * @param string $alias
	 * @return $this
	 */
	public function setTable($table,$alias = NULL)
	{
		if(isset($alias) && is_string($table))
		{
			$this->table = array($alias=>$table);
		}
		else 
		{
			$this->table = $table;
		}
		return $this;
	}

	/**
	 * Alias of setTable()
	 * @param string | Query $table
	 * @param string $alias
	 * @return Query
	 */
	public function fromTable($table, $alias = NULL)
	{
		return $this->setTable($table,$alias);
	}

	/**
	 * @param IConnection $connection
	 * @return $this
	 */
	public function setConnection(IConnection $connection)
	{
		$this->connection = $connection;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isParameterized(): bool
	{
		return $this->parameterized;
	}

	/**
	 * @param bool $parameterized
	 * @return IQuery
	 */
	public function setParameterized(bool $parameterized): IQuery
	{
		$this->parameterized = $parameterized;
		foreach($this->where as $condition) {
			$condition->setParameterized($parameterized);
		}
		return $this;
	}

	/**
	 * @param bool $parameterized
	 * @return string
	 */
	abstract function build(bool $parameterized = null);
	
	/**
	 * Executes the query and returns the result.
	 * @param IConnection $connection - the database connection to execute the quote upon.
	 * @return Statement | bool
	 * @throws QueryException
	 */
	public function execute(IConnection $connection = NULL)
	{
		if(isset($connection))
			$this->setConnection($connection);

		if(!($this->connection instanceof IConnection))
		{
			try 
			{
				$this->connection = Connection::get();
			}
			catch (Exception $e)
			{
				throw new QueryException('No Database Connection', Error::DB_ERROR);
			}
		}

		$statement = $this->connection->prepare($this->build(true));
		if($statement !== false)
		{
			$this->bindParametersToStatement($statement);
			if($statement->execute() === true)
				return $statement;
			else
				throw new QueryException(json_encode($statement->errorInfo()));
		}
		else
			throw new QueryException('Failed to prepare statement for execution.');
	}

	/**
	 * @param IStatement $statement
	 * @throws QueryException
	 */
	private function bindParametersToStatement(IStatement &$statement)
	{
		foreach($this->getParams() as $name=>$value)
		{
			switch(gettype($value))
			{
				case "boolean":
					$type = PDO::PARAM_BOOL;
					break;
				case "integer":
					$type = PDO::PARAM_INT;
					break;
				case "double":
					break;
				case "string":
					$type = PDO::PARAM_STR;
					break;
				case "array":
					$value = implode(',', $value);
					$type = PDO::PARAM_STR;
					break;
				case "object":
					try
					{
						$value = (string)$value;
					}
					catch(Exception $e) {
						throw new QueryException('Could not convert object to string for query.', 0, $e);
					}
					$type = PDO::PARAM_STR;
					break;
				case "resource":
				case "resource (closed)":
					throw new QueryException('Cannot supply a resource to a query.');
					break;
				case "NULL":
					$type = PDO::PARAM_NULL;
					break;
				default:
					throw new QueryException('Unable to determine parameter type.');
			}

			//Check for a large value
			if($type === PDO::PARAM_STR && strlen($value) > 4000)
				$type = PDO::PARAM_LOB;

			$statement->bindParam($name, $value, $type);
		}
	}

	/**
	 * Prepares the query before executing and returns the result.
	 *
	 * @param IConnection $connection
	 * @return Statement
	 * @throws QueryException
	 * @throws PDOException
	 */
	public function prepareAndExecute(IConnection $connection = null)
	{
		if(isset($connection))
			$this->setConnection($connection);

		$driverOptions = $this->connection->getDriverOptions();

		if($this->connection instanceof Connection)
		{
			/** @var Statement $stmt */
			$stmt = $this->connection->prepare($this->build(), $driverOptions);
			if($stmt !== false)
				$stmt->execute();
			else
				throw new QueryException('Failed to prepare query. Error: ' . serialize($this->connection->errorInfo()));

			return $stmt;
		}
		else
		{
			try
			{
				$this->connection = Connection::get();
			}
			catch(Exception $e)
			{
				throw new QueryException('No Database Connection', Error::DB_ERROR);
			}
			if($this->connection instanceof Connection)
			{
				/** @var Statement $stmt */
				$stmt = $this->connection->prepare($this->build(), $driverOptions);
				if($stmt !== false)
					$stmt->execute();
				else
					throw new QueryException('Failed to prepare query.');

				return $stmt;
			}
		}

		throw new QueryException('No Database Connection', Error::DB_ERROR);
	}

	/**
	 * This method gets either the default framework connection or a predefined named connection.
	 * @param string $namedConnection
	 * @return Connection
	 * @throws Exception
	 */
	public static function connection($namedConnection = NULL)
	{
		if($namedConnection == NULL)
		{
			$db = Connection::getInstance();
		}
		else
		{
			$db = Connection::getNamedConnection($namedConnection);
		}

		return $db;
	}
	
	/*-----------------------------------------------WHERE CLAUSES-----------------------------------------------*/
	
	public function addWhere(Condition $where)
	{
		$this->where[] = $where;
		return $this;
	}
	
	public function clearWhere()
	{
		$this->where = array();
		return $this;
	}
	
	public function whereCondition($column, $operator, $value, $columnJoin = NULL)
	{
		$this->addWhere(Condition::get($column, $operator, $value, $columnJoin));
		return $this;
	}
	
	/**
	 * An open ended where statement
	 * @param string | Select $statement
	 * @return $this
	 */
	public function whereStatement($statement)
	{
		$this->addWhere(Condition::statement($statement));
		return $this;
	}
	
	/**
	 * SQL WHERE =
	 * @param string $column
	 * @param mixed $value
	 * @param string $paramName
	 * @param bool $columnJoin
	 * @param bool $parameterized
	 * @return $this
	 */
	public function whereEqual(string $column, $value, string $paramName = null, $columnJoin = null, bool $parameterized = null)
	{
		if(!isset($parameterized))
			$parameterized = $this->isParameterized();
		$this->addWhere(Condition::equal($column, $value, $paramName, $columnJoin, $parameterized));
		return $this;
	}

	/**
	 * SQL WHERE <>
	 * @param string $column
	 * @param mixed $value
	 * @param boolean $columnJoin
	 * @return $this
	 */
	public function whereNotEqual($column, $value, $columnJoin = NULL)
	{
		$this->addWhere(Condition::notEqual($column, $value, $columnJoin));
		return $this;
	}
	
	/**
	 * SQL LIKE Clause
	 * @param string $column
	 * @param mixed $value
	 * @return $this
	 */
	public function whereLike($column, $value)
	{
		$this->addWhere(Condition::like($column, $value));
		return $this;
	}

	/**
	 * SQL NOT LIKE Clause
	 * @param string $column
	 * @param mixed $value
	 * @return $this
	 */
	public function whereNotLike($column, $value)
	{
		$this->addWhere(Condition::notLike($column, $value));
		return $this;
	}
	
	/**
	 * SQL IS NULL Clause
	 * @param string $column
	 * @return $this
	 */
	public function whereNull($column)
	{
		$this->addWhere(Condition::null($column));
		return $this;
	}
	
	/**
	 * SQL IN Clause
	 * @param string $column
	 * @param array | Select $values
	 * @return $this
	 */
	public function whereIn($column, $values)
	{
		$this->addWhere(Condition::in($column, $values));
		return $this;
	}
	
	/**
	 * SQL BETWEEN Clause
	 * @param string $column
	 * @param mixed $start
	 * @param mixed $end
	 * @return $this
	 * @throws Exception
	 */
	public function whereBetween($column, $start, $end)
	{
		$this->addWhere(Condition::between($column, $start, $end));
		return $this;
	}
	
	/*-----------------------------------------------UTILITY FUNCTIONS-----------------------------------------------*/
	
	/**
	 * Converts a PHP data type into a compatible MySQL string.
	 * @param mixed $inValue
	 * @param IConnection $db
	 * @throws QueryException
	 * @return string
	 */
	public static function convertTypes($inValue, IConnection $db = NULL)
	{
		if(!($db instanceof IConnection))
		{
			try{
				$db = Connection::get();
			}
			catch (Exception $e)
			{
				throw new QueryException('No Database Connection', Error::DB_ERROR);
			}
		}
		
		//Decided to error on the side of caution and represent floats as strings in SQL statements
		if(is_int($inValue))
		{
			return (int)$inValue;
		}
		if(is_string($inValue) || is_float($inValue))
		{
			return $db->quote($inValue);
		}
		elseif(is_bool($inValue))
		{
			//Switch based on driver used in the connection.
			switch ($db->getDriver())
			{
				case Connection::DRIVER_MYSQL:
					return ($inValue) ? 'TRUE' : 'FALSE';
				default:
					return ($inValue) ? '1' : '2';
			}
		}
		elseif(is_null($inValue))
		{
			return 'NULL';
		}
		elseif(is_array($inValue))
		{
			return $db->quote(implode(" ", $inValue));
		}
		elseif($inValue instanceof DateTime)
		{
			//@todo add a switch in here for different database types
			return $db->quote($inValue->format('Y-m-d H:i:s'));
		}
		else
		{
			return $db->quote((string)$inValue);
		}
	}

	/*-----------------------------------------------FACTORY METHODS-----------------------------------------------*/

	/**
	 * Construct and return an instance of the child object.
	 *
	 * @param string $table
	 * @return static
	 * @throws QueryException
	 */
	public static function table($table)
	{
		return new static($table);
	}

	/**
	 * Construct a Select query object and return it.
	 *
	 * @param string $table
	 * @param array $columns
	 * @param IConnection $db
	 * @param array | string $order
	 * @param Pager | int $limit
	 * @param bool $parameterized
	 * @return Select
	 * @throws QueryException
	 */
	public static function select($table = NULL, array $columns = NULL, IConnection $db = NULL, $order = NULL, $limit = NULL, bool $parameterized = null)
	{
		return new Select($table, $columns, $db, $order, $limit, $parameterized);
	}

	/**
	 * Construct and return an Insert query object.
	 *
	 * @param string $table
	 * @param array $data
	 * @param IConnection
	 * @param string $priority
	 * @param bool $parameterized
	 * @return Insert
	 * @throws QueryException
	 */
	public static function insert($table = NULL, $data = NULL, IConnection $db = NULL, $priority = NULL, bool $parameterized = null)
	{
		return new Insert($table, $data, $db, $priority, $parameterized);
	}

	/**
	 * Construct and return an Update query object.
	 *
	 * @param string $table
	 * @param array $data
	 * @param IConnection $db
	 * @param array | string $order
	 * @param Pager | int $limit
	 * @param bool $parameterized
	 * @return Update
	 * @throws QueryException
	 */
	public static function update($table = NULL, array $data = NULL, IConnection $db = NULL, $order = NULL, $limit = NULL, bool $parameterized = null)
	{
		return new Update($table, $data, $db, $order, $limit, $parameterized);
	}

	/**
	 * Construct and return a Delete query object.
	 *
	 * @param string $table
	 * @param IConnection $db
	 * @param bool $parameterized
	 * @return Delete
	 * @throws QueryException
	 */
	public static function delete($table = NULL, IConnection $db = NULL, bool $parameterized = null)
	{
		return new Delete($table, $db, $parameterized);
	}

	/**
	 * Construct and return a Union query object
	 *
	 * @param array $queries
	 * @param IConnection $db
	 * @return Union
	 * @throws QueryException
	 */
	public static function union(array $queries = array(), IConnection $db = NULL)
	{
		return new Union($queries, $db);
	}

	/**
	 * Create and return a Query DataSet object
	 *
	 * @param array $data
	 * @param IConnection $connection
	 * @return DataSet
	 */
	public static function dataSet(array $data = NULL, IConnection $connection = NULL)
	{
		return new DataSet($data,$connection);
	}

    /**
     * Execute a raw SQL statement
     * @param string | Query $statement
     * @param IConnection $connection
     * @return Statement
	 * @throws ConfigurationException
     */
	public static function raw($statement, IConnection $connection = NULL)
	{
        if(isset($connection))
            return $connection->query($statement);
        else
            return Connection::get()->query($statement);
	}

	/**
	 * Create a prepared statement for a stored procedure call.
	 * @param string $procedureName
	 * @param array $parameters
	 * @param IConnection $connection
	 * @param array $driverOptions
	 * @return PDOStatement
	 * @throws QueryException
	 * @throws ConfigurationException
	 */
	public static function procedure($procedureName, array $parameters = NULL, IConnection $connection = NULL, array $driverOptions = [])
	{
		if(!isset($connection))
			$connection = Connection::get();

		$execStatement = self::getConnectionSpecificExecuteStatement($procedureName, $parameters, $connection);

		//SqlSrv Cannot return a different object yet.
		if($connection->getDriver() != Connection::DRIVER_SQLSRV)
			$driverOptions[PDO::FETCH_CLASS] = '\Staple\Query\Statement';

		//Prepare Statement
		$stmt = $connection->prepare($execStatement, $driverOptions);

		//Bind Values
		if(isset($parameters))
		{
			foreach ($parameters as $key => $value)
			{
				//Figure out data type
				if (is_int($value))
					$type = PDO::PARAM_INT;
				elseif (is_bool($value))
					$type = PDO::PARAM_BOOL;
				elseif (is_null($value))
					$type = PDO::PARAM_NULL;
				else
					$type = PDO::PARAM_STR;

				$stmt->bindValue($key, $value, $type);
			}
		}

		return $stmt;
	}

	/**
	 * Get connection specific execute statement string
	 * @param string $procedureName
	 * @param array &$parameters
	 * @param IConnection $connection
	 * @return string
	 * @throws QueryException
	 */
	public static function getConnectionSpecificExecuteStatement($procedureName, array &$parameters, IConnection $connection)
	{
		//Grab the appropriate exec string
		switch($connection->getDriver())
		{
			case Connection::DRIVER_SQLSRV:
				return self::composeSqlSrvProcedureString($procedureName, $parameters);
				break;
			case Connection::DRIVER_MYSQL:
				return self::composeMySqlProcedureString($procedureName, $parameters);
				break;
			default:
				throw new QueryException('Could not find a string generator for your database driver.');
				break;
		}
	}

	/**
	 * Generate the MySQL stored procedure execution string.
	 * @param string $procedureName
	 * @param array &$parameters
	 * @return string
	 * @throws QueryException
	 */
	public static function composeMySqlProcedureString($procedureName, array &$parameters)
	{
		$params = '(';

		$numericKeys = false;
		foreach ($parameters as $key=>$value)
		{
			if (is_int($key))
			{
				$numericKeys = true;
				break;
			}
			elseif(substr($key,0,1) != ':')
			{
				unset($parameters[$key]);
				$parameters[':'.$key] = $value;
			}
			else
			{
				if ($numericKeys == true)
				{
					throw new QueryException('You cannot mix numeric and named parameter keys.');
				}
			}
		}

		$keys = array_keys($parameters);
		if($numericKeys == true)
		{
			for($i = 0; $i<count($keys); $i++)
			{
				$params .= '?, ';
			}
			$params = substr($params,0,strlen($params)-2);
		}
		else
			$params .= implode(', ',$keys);

		$params .= ')';

		return 'CALL '.$procedureName.$params;
	}

	/**
	 * Generate the SQL Server stored procedure execution string.
	 * @param string $procedureName
	 * @param array &$parameters
	 * @return string
	 * @throws QueryException
	 */
	public static function composeSqlSrvProcedureString($procedureName, array &$parameters)
	{
		$params = ' ';
		foreach ($parameters as $key=>$value)
		{
			if (is_int($key))
				throw new QueryException('You must specify the procedure variable name as the array key.');
			elseif(substr($key,0,1) != '@')
			{
				unset($parameters[$key]);
				$parameters['@'.$key] = $value;
			}
		}

		$keys = array_keys($parameters);
		$params .= implode(' = ?, ',$keys);
		$params .= ' = ?';

		//Fix parameters array for PDO binding
		$parameters = array_values($parameters);
		for($i = count($parameters); $i > 0; $i--)
		{
			$tmp = $parameters[$i-1];
			unset($parameters[$i-1]);
			$parameters[$i] = $tmp;
		}
		ksort($parameters);

		return 'EXEC '.$procedureName.$params;
	}
}