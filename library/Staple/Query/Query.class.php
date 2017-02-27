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
use PDOStatement;
use Staple\Error;
use Staple\Exception\QueryException;
use Staple\Pager;

abstract class Query
{
	
	/**
	 * Table to act upon.
	 * @var mixed
	 */
	public $table;
	
	/**
	 * The Connection database object. A database object is required to properly escape input.
	 * @var Connection
	 */
	protected $connection;
	
	/**
	 * An array of Where Clauses. The clauses are additive, using the AND  conjunction.
	 * @var array[Staple_Query_Condition]
	 */
	protected $where = array();

	/**
	 * @param string $table
	 * @param Connection $db
	 * @throws QueryException
	 */
	public function __construct($table = NULL, Connection $db = NULL)
	{
		if($db instanceof Connection)
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
		if(!($this->connection instanceof Connection))
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
	 * @return Connection $db
	 */
	public function getConnection()
	{
		return $this->connection;
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
	 * @param Connection $connection
	 * @return $this
	 */
	public function setConnection(Connection $connection)
	{
		$this->connection = $connection;
		return $this;
	}

	/**
	 * @return string
	 */
	abstract function build();
	
	/**
	 * Executes the query and returns the result.
	 * @param Connection $connection - the database connection to execute the quote upon.
	 * @return Statement | bool
	 * @throws Exception
	 */
	public function execute(Connection $connection = NULL)
	{
		if(isset($connection))
			$this->setConnection($connection);

		if($this->connection instanceof Connection)
		{
			return $this->connection->query($this->build());
		}
		else
		{
			try 
			{
				$this->connection = Connection::get();
			}
			catch (Exception $e)
			{
				throw new QueryException('No Database Connection', Error::DB_ERROR);
			}
			if($this->connection instanceof Connection)
			{
				return $this->connection->query($this->build());
			}
		}
		return false;
	}

	/**
	 * Prepares the query before executing and returns the result.
	 *
	 * @param Connection $connection
	 * @return Statement
	 * @throws QueryException
	 * @throws \PDOException
	 */
	public function prepareAndExecute(Connection $connection = null)
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
	 * @param boolean $columnJoin
	 * @return $this
	 */
	public function whereEqual($column, $value, $columnJoin = NULL)
	{
		$this->addWhere(Condition::equal($column, $value, $columnJoin));
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
	 * @param Connection $db
	 * @throws QueryException
	 * @return string
	 */
	public static function convertTypes($inValue, Connection $db = NULL)
	{
		if(!($db instanceof Connection))
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
	 * @param Connection $db
	 * @param array | string $order
	 * @param Pager | int $limit
	 * @return Select
	 */
	public static function select($table = NULL, array $columns = NULL, Connection $db = NULL, $order = NULL, $limit = NULL)
	{
		return new Select($table, $columns, $db, $order, $limit);
	}

	/**
	 * Construct and return an Insert query object.
	 *
	 * @param string $table
	 * @param array $data
	 * @param Connection
	 * @param string $priority
	 * @return Insert
	 */
	public static function insert($table = NULL, $data = NULL, Connection $db = NULL, $priority = NULL)
	{
		return new Insert($table, $data, $db, $priority);
	}

	/**
	 * Construct and return an Update query object.
	 *
	 * @param string $table
	 * @param array $data
	 * @param Connection $db
	 * @param array | string $order
	 * @param Pager | int $limit
	 * @return Update
	 */
	public static function update($table = NULL, array $data = NULL, Connection $db = NULL, $order = NULL, $limit = NULL)
	{
		return new Update($table, $data, $db, $order, $limit);
	}

	/**
	 * Construct and return a Delete query object.
	 *
	 * @param string $table
	 * @param Connection $db
	 * @return Delete
	 */
	public static function delete($table = NULL, Connection $db = NULL)
	{
		return new Delete($table, $db);
	}

	/**
	 * Construct and return a Union query object
	 *
	 * @param array $queries
	 * @param Connection $db
	 * @return Union
	 */
	public static function union(array $queries = array(), Connection $db = NULL)
	{
		return new Union($queries, $db);
	}

	/**
	 * Create and return a Query DataSet object
	 *
	 * @param array $data
	 * @param Connection $connection
	 * @return DataSet
	 */
	public static function dataSet(array $data = NULL, Connection $connection = NULL)
	{
		return new DataSet($data,$connection);
	}

    /**
     * Execute a raw SQL statement
     * @param string | Query $statement
     * @param Connection $connection
     * @return Statement
     */
	public static function raw($statement, Connection $connection = NULL)
	{
        if(isset($connection))
            return $connection->query($statement);
        else
            return Connection::get()->query($statement);
	}

	/**
	 * Create a prepared statement for a stored procedure call.
	 * @param $name
	 * @param array $parameters
	 * @param Connection $connection
	 * @param array $driverOptions
	 * @return PDOStatement
	 * @throws QueryException
	 */
	public static function procedure($name, array $parameters = NULL, Connection $connection = NULL, array $driverOptions = [])
	{
		if(isset($connection))
		{
			$myConn = $connection;
		}
		else
		{
			$myConn = Connection::get();
		}

		if(isset($parameters))
		{
			$params = '(';

			$numericKeys = false;
			foreach ($parameters as $key=>$value)
			{
				if (is_int($key))
				{
					$numericKeys = true;
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
			}
			else
				$params .= implode(', ',$keys);

			$params .= ')';
		}
		else
		{
			$params = '()';
		}

		$driverOptions[PDO::FETCH_CLASS] = '\Staple\Query\Statement';

		//Prepare Statement
		$stmt = $myConn->prepare('CALL '.$name.$params, $driverOptions);

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
}