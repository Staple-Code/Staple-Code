<?php

/** 
 * A class for building database queries.
 * Right now the class only supports the MySQL database.
 * 
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
 * 
 */
abstract class Staple_Query
{
	
	/**
	 * Table to act upon.
	 * @var mixed
	 */
	public $table;
	
	/**
	 * The database object. A database object is required to properly escape input.
	 * @var mysqli
	 */
	protected $db;
	
	/**
	 * An array of Where Clauses. The clauses are additive, using the AND  conjunction.
	 * @var array[Staple_Query_Condition]
	 */
	protected $where = array();
	
	
	public function __construct($table = NULL, mysqli $db = NULL)
	{
		if($db instanceof mysqli)
		{
			$this->setDb($db);
		}
		else
		{
			try {
				$this->setDb(Staple_DB::get());
			}
			catch (Exception $e)
			{
				$this->setDb(new mysqli());
			}
		}
		if(!($this->db instanceof mysqli))
		{
			throw new Exception('Unable to create database object', Staple_Error::DB_ERROR);
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
	 * @return the $table
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @return the $db
	 */
	public function getDb()
	{
		return $this->db;
	}

	/**
	 * @param mixed $table
	 * @param string $alias
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
	 * @param mysqli $db
	 */
	public function setDb(mysqli $db)
	{
		$this->db = $db;
		return $this;
	}

	abstract function build();
	
	/**
	 * Executes the query and returns the result.
	 * @return mysqli_result | bool
	 */
	public function Execute()
	{
		if($this->db instanceof mysqli)
		{
			return $this->db->query($this->build());
		}
		else
		{
			try 
			{
				$this->db = Staple_DB::get();
			}
			catch (Exception $e)
			{
				//@todo try for a default connection if no staple connection
				throw new Exception('No Database Connection', Staple_Error::DB_ERROR);
			}
			if($this->db instanceof mysqli)
			{
				return $this->db->query($this->build());
			}
		}
		return false;
	}
	
	
	/*-----------------------------------------------WHERE CLAUSES-----------------------------------------------*/
	
	public function addWhere(Staple_Query_Condition $where)
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
		$this->addWhere(Staple_Query_Condition::Get($column, $operator, $value, $columnJoin));
		return $this;
	}
	
	/**
	 * An open ended where statement
	 * @param string | Staple_Query_Select $statement
	 */
	public function whereStatement($statement)
	{
		$this->addWhere(Staple_Query_Condition::Statement($statement));
		return $this;
	}
	
	/**
	 * SQL WHERE =
	 * @param string $column
	 * @param mixed $value
	 * @param boolean $columnJoin
	 */
	public function whereEqual($column, $value, $columnJoin = NULL)
	{
		$this->addWhere(Staple_Query_Condition::Equal($column, $value, $columnJoin));
		return $this;
	}
	
	/**
	 * SQL LIKE Clause
	 * @param string $column
	 * @param mixed $value
	 */
	public function whereLike($column, $value)
	{
		$this->addWhere(Staple_Query_Condition::Like($column, $value));
		return $this;
	}
	
	/**
	 * SQL IS NULL Clause
	 * @param string $column
	 */
	public function whereNull($column)
	{
		$this->addWhere(Staple_Query_Condition::Null($column));
		return $this;
	}
	
	/**
	 * SQL IN Clause
	 * @param string $column
	 * @param array | Staple_Query_Select $values
	 */
	public function whereIn($column, $values)
	{
		$this->addWhere(Staple_Query_Condition::In($column, $values));
		return $this;
	}
	
	/**
	 * SQL BETWEEN Clause
	 * @param string $column
	 * @param mixed $start
	 * @param mixed $end
	 */
	public function whereBetween($column, $start, $end)
	{
		$this->addWhere(Staple_Query_Condition::Between($column, $start, $end));
		return $this;
	}
	
	/*-----------------------------------------------UTILITY FUNCTIONS-----------------------------------------------*/
	
	/**
	 * Converts a PHP data type into a compatible MySQL string.
	 * @param mixed $inValue
	 * @return string
	 */
	public static function convertTypes($inValue, Staple_DB $db = NULL)
	{
		if(!($db instanceof mysqli))
		{
			try{
				$db = Staple_DB::get();
			}
			catch (Exception $e)
			{
				throw new Exception('No Database Connection', Staple_Error::DB_ERROR);
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
}

?>