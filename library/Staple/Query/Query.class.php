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

use \Exception;
use \Staple\Error;
use \Staple\DB;
use \mysqli;
use \DateTime;
use Staple\Pager;
use \PDO;
use \PDOStatement;

abstract class Query
{
	
	/**
	 * Table to act upon.
	 * @var mixed
	 */
	public $table;
	
	/**
	 * The database object. A database object is required to properly escape input.
	 * @var PDO
	 */
	protected $db;
	
	/**
	 * An array of Where Clauses. The clauses are additive, using the AND  conjunction.
	 * @var array[Staple_Query_Condition]
	 */
	protected $where = array();
	
	
	public function __construct($table = NULL, PDO $db = NULL)
	{
		if($db instanceof PDO)
		{
			$this->setDb($db);
		}
		else
		{
			try {
				$this->setDb(Connection::get());
			}
			catch (Exception $e)
			{
				throw new Exception('Unable to find a database connection.', Error::DB_ERROR, $e);
			}
		}
		if(!($this->db instanceof PDO))
		{
			throw new Exception('Unable to create database object', Error::DB_ERROR);
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
	 * @return PDO $db
	 */
	public function getDb()
	{
		return $this->db;
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
	 * @param PDO $db
	 * @return $this
	 */
	public function setDb(PDO $db)
	{
		$this->db = $db;
		return $this;
	}

	/**
	 * @return string
	 */
	abstract function build();
	
	/**
	 * Executes the query and returns the result.
	 * @param PDO $connection - the database connection to execute the quote upon.
	 * @return PDOStatement | bool
	 * @throws Exception
	 */
	public function execute(PDO $connection = NULL)
	{
		if(isset($connection))
			$this->setDb($connection);

		if($this->db instanceof PDO)
		{
			return $this->db->query($this->build());
		}
		else
		{
			try 
			{
				$this->db = Connection::get();
			}
			catch (Exception $e)
			{
				//@todo try for a default connection if no staple connection
				throw new Exception('No Database Connection', Error::DB_ERROR);
			}
			if($this->db instanceof PDO)
			{
				return $this->db->query($this->build());
			}
		}
		return false;
	}

	/**
	 * This method gets either the default framework connection or a predefined named connection.
	 * @param string $namedConnection
	 * @return PDO
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
		$this->addWhere(Condition::Get($column, $operator, $value, $columnJoin));
		return $this;
	}
	
	/**
	 * An open ended where statement
	 * @param string | Select $statement
	 * @return $this
	 */
	public function whereStatement($statement)
	{
		$this->addWhere(Condition::Statement($statement));
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
		$this->addWhere(Condition::Equal($column, $value, $columnJoin));
		return $this;
	}
	
	/**
	 * SQL LIKE Clause
	 * @param string $column
	 * @param mixed $value
	 */
	public function whereLike($column, $value)
	{
		$this->addWhere(Condition::Like($column, $value));
		return $this;
	}
	
	/**
	 * SQL IS NULL Clause
	 * @param string $column
	 */
	public function whereNull($column)
	{
		$this->addWhere(Condition::Null($column));
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
		$this->addWhere(Condition::In($column, $values));
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
		$this->addWhere(Condition::Between($column, $start, $end));
		return $this;
	}
	
	/*-----------------------------------------------UTILITY FUNCTIONS-----------------------------------------------*/
	
	/**
	 * Converts a PHP data type into a compatible MySQL string.
	 * @param mixed $inValue
	 * @return string
	 */
	public static function convertTypes($inValue, DB $db = NULL)
	{
		if(!($db instanceof mysqli))
		{
			try{
				$db = DB::get();
			}
			catch (Exception $e)
			{
				throw new Exception('No Database Connection', Error::DB_ERROR);
			}
		}
		
		//Decided to error on the side of caution and represent floats as strings in SQL statements
		if(is_string($inValue) || is_float($inValue))
		{
			return "'".$db->real_escape_string($inValue)."'";
		}
		elseif(is_bool($inValue))
		{
			return ($inValue) ? 'TRUE' : 'FALSE';
		}
		elseif(is_null($inValue))
		{
			return 'NULL';
		}
		elseif(is_array($inValue))
		{
			return "'".$db->real_escape_string(implode(" ", $inValue))."'";
		}
		elseif($inValue instanceof DateTime)
		{
			return "'".$db->real_escape_string($inValue->format('Y-m-d H:i:s'))."'";
		}
		else
		{
			return "'".$db->real_escape_string((string)$inValue)."'";
		}
	}

	/*-----------------------------------------------FACTORY METHODS-----------------------------------------------*/

	/**
	 * Construct and return an instance of the child object.
	 *
	 * @param string $table
	 * @return Query
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
	 * @param DB $db
	 * @param array | string $order
	 * @param Pager | int $limit
	 * @return Select
	 */
	public static function select($table = NULL, array $columns = NULL, PDO $db = NULL, $order = NULL, $limit = NULL)
	{
		return new Select($table, $columns, $db, $order, $limit);
	}

	/**
	 * Construct and return an Insert query object.
	 *
	 * @param string $table
	 * @param array $data
	 * @param DB $db
	 * @param string $priority
	 * @return Insert
	 */
	public static function insert($table = NULL, $data = NULL, $db = NULL, $priority = NULL)
	{
		return new Insert($table, $data, $db, $priority);
	}

	/**
	 * Construct and return an Update query object.
	 *
	 * @param string $table
	 * @param array $data
	 * @param DB $db
	 * @param array | string $order
	 * @param Pager | int $limit
	 * @return Update
	 */
	public static function update($table = NULL, array $data = NULL, $db = NULL, $order = NULL, $limit = NULL)
	{
		return new Update($table, $data, $db, $order, $limit);
	}

	/**
	 * Construct and return a Delete query object.
	 *
	 * @param string $table
	 * @param mysqli $db
	 */
	public static function delete($table = NULL, mysqli $db = NULL)
	{
		return new Delete($table, $db);
	}

	/**
	 * Construct and return a Union query object
	 *
	 * @param array $queries
	 * @param mysqli $db
	 */
	public static function union(array $queries = array(), mysqli $db = NULL)
	{
		return new Union($queries, $db);
	}

	/**
	 * Execute a raw SQL statement
	 * @param $statement
	 */
	public static function raw($statement)
	{
		//@todo this function should just accept SQL and execute it in place returning the result.
	}
}

?>