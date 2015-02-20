<?php
 
/** 
 * A class for creating SQL SELECT statements
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

use \Staple\Error;
use \Staple\Pager;
use \Exception;
use \PDO;

class Select extends Query
{
	const ALL = 'ALL';
	const DISTINCT = 'DISTINCT';
	const DISTINCTROW = 'DISTINCTROW';
	const HIGH_PRIORITY = 'HIGH_PRIORITY';
	const STRAIGHT_JOIN = 'STRAIGHT_JOIN';
	const SQL_SMALL_RESULT = 'SQL_SMALL_RESULT';
	const SQL_BIG_RESULT = 'SQL_BIG_RESULT';
	const SQL_BUFFER_RESULT = 'SQL_BUFFER_RESULT';
    const SQL_CACHE = 'SQL_CACHE';
    const SQL_NO_CACHE = 'SQL_NO_CACHE';
    const SQL_CALC_FOUND_ROWS = 'SQL_CALC_FOUND_ROWS';
	
    /**
     * Additional Query Flags
     * @var array[string]
     */
	protected $flags = array();
	/**
	 * The columns with which to act upon.
	 * @var array[string]
	 */
	public $columns = array();
	/**
	 * Holds the order of the SQL query. It can be either a string or an array of the columns to order by.
	 * @var string | array
	 */
	protected $order;
	/**
	 * Limit number of rows to return.
	 * @var int
	 */
	protected $limit;
	/**
	 * The Limit Offset. Used to skip a number of rows before selecting.
	 * @var int
	 */
	protected $limitOffset;
	/**
	 * Stores the GROUP BY columns;
	 * @var array | string
	 */
	protected $groupBy;
	/**
	 * An array that holds the HAVING clauses
	 * @var Condition[]
	 */
	protected $having = array();
	/**
	 * Array of Staple_Query_Join objects that represent table joins on the query
	 * @var Join[]
	 */
	protected $joins = array();

	/**
	 * @param string $table
	 * @param array $columns
	 * @param PDO $db
	 * @param array | string $order
	 * @param Pager | int $limit
	 * @throws Exception
	 */
	public function __construct($table = NULL, array $columns = NULL, PDO $db = NULL, $order = NULL, $limit = NULL)
	{
		parent::__construct(NULL, $db);
		
		//Set Table
		if(isset($table))
		{
			$this->setTable($table);
		}
		//Set Columns
		if(isset($columns))
		{
			$this->setColumns($columns);
		}
		//Set Order
		if(isset($order))
		{
			$this->orderBy($order);
		}
		//Set Limit
		if(isset($limit))
		{
			$this->limit($limit);
		}
	}
	
	public function addFlag($flag)
	{
		switch($flag)
		{
			case self::ALL:
			case self::DISTINCT:
			case self::DISTINCTROW:
			case self::HIGH_PRIORITY:
			case self::STRAIGHT_JOIN:
			case self::SQL_SMALL_RESULT:
			case self::SQL_BIG_RESULT:
			case self::SQL_BUFFER_RESULT:
		    case self::SQL_CACHE:
		    case self::SQL_NO_CACHE:
		    case self::SQL_CALC_FOUND_ROWS:
		    	$this->flags[] = $flag;
		    	break;
		}
		return $this;
	}
	
	public function clearFlags()
	{
		$this->flags = array();
		return $this;
	}

	/**
	 * @return array $columns
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Returns array | string order.
	 * @return string | array
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * @return array | string $groupBy
	 */
	public function getGroupBy()
	{
		return $this->groupBy;
	}

	/**
	 * @return Pager | int $limit
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * @return int $limitOffset
	 */
	public function getLimitOffset()
	{
		return $this->limitOffset;
	}

	/**
	 * @param mixed $table
	 * @param string $alias
	 */
	public function setTable($table,$alias = NULL)
	{
		if(is_array($table))
		{
			$this->table = $table;
		}
		elseif($table instanceof Query || $table instanceof Union)
		{
			if(!isset($alias))
			{
				throw new Exception('Every derived table must have its own alias', Error::DB_ERROR);
			}
			$this->table = array($alias=>$table);
		}
		elseif(isset($alias) && is_string($table))
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
	 * Different from addColumnsArray(), this function replaces all existing columns in the query.
	 */
	public function setColumns(array $columns)
	{
		$this->columns = array();
		foreach($columns as $name=>$col)
		{
			if(is_string($name))
			{
				$this->columns[$name] = $col;
			}
			else
			{
				$this->columns[] = (string)$col;
			}
		}
		return $this;
	}
	
	/**
	 * Set the order.
	 * @param string | array $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
		return $this;
	}
	
	/**
	 * @param string | array $groupBy
	 */
	public function setGroupBy($groupBy)
	{
		$this->groupBy = $groupBy;
		return $this;
	}

	/**
	 * @param int $limit
	 * @return Select
	 */
	public function setLimit($limit)
	{
		$this->limit = (int)$limit;
		return $this;
	}
	
	/**
	 * @param int $limitOffset
	 * @return Select
	 */
	public function setLimitOffset($limitOffset)
	{
		$this->limitOffset = (int)$limitOffset;
		return $this;
	}

	/**
	 * Add to the list of columns. Optional parameter to name a column.
	 * @param string | Select $col
	 * @param string $name
	 * @return $this
	 */
	public function addColumn($col,$name = NULL)
	{
		if($col instanceof Query)
			$col = '('.(string)$col.')';
		
		if(isset($name))
		{
			$this->columns[(string)$name] = $col;
		}
		else
		{
			$this->columns[] = (string)$col;
		}
		
		return $this;
	}
	
	/**
	 * Add an array of columns to the list of selected columns
	 * @param array $columns
	 */
	public function addColumnsArray(array $columns)
	{
		foreach($columns as $name=>$col)
		{
			if(is_string($name))
			{
				$this->columns[$name] = $col;
			}
			else
			{
				$this->columns[] = (string)$col;
			}
		}
		return $this;
	}

	/**
	 * An alias of setColumns()
	 * @param array $cols
	 * @return Select
	 */
	public function columns(array $cols)
	{
		return $this->setColumns($cols);
	}
	
	/**
	 * Remove a column from the $columns array.
	 * @param string $col
	 */
	public function removeColumn($col)
	{
		if(($key = array_search($col, $this->columns)) !== false)
		{
			unset($this->columns[$key]);
			return true;
		}
		return false;
	}
	
	/**
	 * Remove a column from the $columns property by it's alias.
	 * @param string $name
	 */
	public function removeColumnByName($name)
	{
		if(array_key_exists($name, $this->columns))
		{
			unset($this->columns[$name]);
			return true;
		}
		return false;
	}
	
	/**
	 * Alias of setOrder()
	 * @see self::setOrder()
	 */
	public function orderBy($order)
	{
		return $this->setOrder($order);
	}
	
	/**
	 * Alias of setGroupBy()
	 * @param string | array $group
	 * @see self::setGroupBy()
	 */
	public function groupBy($group)
	{
		return $this->setGroupBy($group);
	}
	
	/**
	 * Sets the limit and the offset in one function.
	 * @param int | Pager $limit
	 * @param int $offset
	 * @return $this
	 */
	public function limit($limit,$offset = NULL)
	{
		if($limit instanceof Pager)
		{
			$this->setLimit($limit->getItemsPerPage());
			$this->setLimitOffset($limit->getStartingItem());
		}
		else
		{		
			$this->setLimit($limit);
			if(isset($offset))
				$this->setLimitOffset($offset);
		}
		return $this;
	}

	/*-----------------------------------------------HAVING CLAUSES-----------------------------------------------*/
	
	public function addHaving(Condition $having)
	{
		$this->having[] = $having;
		return $this;
	}
	
	public function clearHaving()
	{
		$this->having = array();
		return $this;
	}
	
	public function havingCondition($column, $operator, $value, $columnJoin = NULL)
	{
		$this->addHaving(Condition::Get($column, $operator, $value, $columnJoin));
		return $this;
	}
	
	public function havingStatement($statement)
	{
		$this->addHaving(Condition::Statement($statement));
		return $this;
	}
	
	public function havingEqual($column, $value, $columnJoin = NULL)
	{
		$this->addHaving(Condition::Equal($column, $value, $columnJoin));
		return $this;
	}
	
	public function havingLike($column, $value)
	{
		$this->addHaving(Condition::Like($column, $value));
		return $this;
	}
	
	public function havingNull($column)
	{
		$this->addHaving(Condition::Null($column));
		return $this;
	}
	
	public function havingIn($column, array $values)
	{
		$this->addHaving(Condition::In($column, $values));
		return $this;
	}
	
	public function havingBetween($column, $start, $end)
	{
		$this->addHaving(Condition::Between($column, $start, $end));
		return $this;
	}
	

	/*-----------------------------------------------JOIN FUNCTIONS-----------------------------------------------*/
	/**
	 * Add a join to the query.
	 * @param Join $join
	 * @return $this
	 */
	public function addJoin(Join $join)
	{
		$this->joins[] = $join;
		return $this;
	}
	
	/**
	 * Remove a join from the query by table name.
	 * @param string $table
	 * @return boolean
	 */
	public function removeJoin($table)
	{
		foreach($this->joins as $key=>$join)
		{
			if($join->getTable() == $table)
			{
				unset($this->joins[$key]);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns true is specified table is already joined to the query, false otherwise.
	 * @param string $table
	 * @return boolean
	 */
	public function isJoined($table)
	{
		foreach($this->joins as $key=>$join)
		{
			if($join->getTable() == $table)
			{
				return true;
			}
		}
		return false;
	}
	
	public function leftJoin($table, $condition)
	{
		$this->addJoin(Join::left($table, $condition));
		return $this;
	}
	
	public function innerJoin($table, $condition)
	{
		$this->addJoin(Join::inner($table, $condition));
		return $this;
	}
	
	/**
	 * Returns the joins array
	 * @return array[Staple_Query_Join]
	 */
	public function getJoins()
	{
		return $this->joins;
	}
	
	/*-----------------------------------------------BUILD FUNCTION-----------------------------------------------*/
	
	/**
	 * 
	 * @see Staple_Query::build()
	 */
	function build()
	{
		$stmt = 'SELECT ';
		
		//Flags
		if(count($this->flags) > 0)
		{
			$stmt .= ' '.implode(' ', $this->flags);
		}
		
		//Collect Select Columns
		if(count($this->columns) == 0)
		{
			$columns = '*';
		}
		else 
		{
			$columns = '';
			foreach($this->columns as $name=>$col)
			{
				//Add commas between columns
				if(strlen($columns) >= 1)
				{
					$columns .= ', ';
				}

				//Wrap sub-selects in parenthesis
				if($col instanceof Select)
				{
					$columns .= '('.$col.')';
				}
				else
				{
					$columns .= $col;
				}

				//Add column aliases where applicable
				if(is_string($name))
				{
					//Switch the method based on database driver of the current connection
					switch($this->getDb()->getDriver())
					{
						case Connection::DRIVER_MYSQL:
							$columns .= " AS `$name`";
							break;
						default:
							$columns .= " AS $name";
					}
				}
			}
		}
		
		//Columns and FROM CLAUSE
		$stmt .= "\n$columns \nFROM ";
		if(is_array($this->table))
		{
			$tables = '';
			foreach($this->table as $name=>$tbl)
			{
				//Add commas between tables
				if(strlen($tables) > 1)
				{
					$tables .= ', ';
				}

				//Wrap subqueries in parenthesis
				if($tbl instanceof Query || $tbl instanceof Union)
				{
					$tables	= '('.$tbl.')';
				}
				else 
				{
					$tables .= $tbl;
				}

				//Add table aliases where applicable
				if(is_string($name))
				{
					//Switch the method based on database driver of the current connection
					switch($this->getDb()->getDriver())
					{
						case Connection::DRIVER_MYSQL:
							$tables .= " AS `$name`";
							break;
						default:
							$tables .= " AS `$name`";
					}
				}
			}
			$stmt .= $tables;
		}
		else
		{
			$stmt .= $this->table;
		}
		
		//JOINS
		if(count($this->joins) > 0)
		{
			$stmt .= "\n".implode("\n", $this->joins);
		}
		
		//WHERE CLAUSE
		if(count($this->where) > 0)
		{
			$stmt .= "\nWHERE ".implode(' AND ', $this->where);
		}
		
		//GROUP BY
		if(isset($this->groupBy))
		{
			$stmt .= "\nGROUP BY ";
			if(is_array($this->groupBy))
			{
				$stmt .= implode(',', $this->groupBy);
			}
			else
			{
				$stmt .= $this->groupBy;
			}
		}
		
		//HAVING
		if(count($this->having) > 0)
		{
			$stmt .= "\nHAVING ".implode(' AND ', $this->having);
		}
		
		//ORDER CLAUSE
		if(isset($this->order))
		{
			$stmt .= "\nORDER BY ";
			if(is_array($this->order))
			{
				$stmt .= implode(', ', $this->order);
			}
			else
			{
				$stmt .= $this->order;
			}
		}
		
		//LIMIT CLAUSE
		if(isset($this->limit))
		{
			$stmt .= "\nLIMIT ".$this->getLimit();
			if(isset($this->limitOffset))
			{
				$stmt .= ' OFFSET '.$this->limitOffset;
			}
		}
		return $stmt;
	}
}

?>