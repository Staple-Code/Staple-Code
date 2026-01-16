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

use Staple\Error;
use Staple\Exception\QueryException;
use Staple\Pager;
use Staple\Traits\Factory;

class Select extends Query implements ISelectQuery
{
	use Factory;

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
	protected array $flags = array();
	/**
	 * The columns with which to act upon.
	 * @var array[string]
	 */
	public array $columns = array();

	/**
	 * Holds the order of the SQL query. It can be either a string or an array of the columns to order by.
	 * @var string | array
	 */
	protected string | array | null $order = null;
	/**
	 * Limit number of rows to return.
	 * @var int
	 */
	protected Pager | int | null $limit = null;
	/**
	 * The Limit Offset. Used to skip a number of rows before selecting.
	 * @var int
	 */
	protected int $limitOffset = 0;
	/**
	 * Stores the GROUP BY columns;
	 * @var array | string
	 */
	protected array | string | null $groupBy = null;
	/**
	 * An array that holds the HAVING clauses
	 * @var Condition[]
	 */
	protected array $having = array();
	/**
	 * Array of Staple_Query_Join objects that represent table joins on the query
	 * @var Join[]
	 */
	protected array $joins = array();

	/**
	 * @param null $table
	 * @param array|null $columns
	 * @param IConnection|null $db
	 * @param string|array|null $order
	 * @param int|null $limit
	 * @param bool|null $parameterized
	 * @throws QueryException
	 */
	public function __construct(mixed $table = NULL, array $columns = NULL, IConnection | null $db = NULL, string | array | null $order = NULL, int | Pager | null $limit = NULL, bool | null $parameterized = null)
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
		//Set Parameterized
		if(isset($parameterized))
		{
			$this->setParameterized($parameterized);
		}
	}

	/**
	 * Add a flag to the SQL statement.
	 * @param $flag
	 * @return $this
	 */
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

	/**
	 * Clears any flags set on the query.
	 *
	 * @return $this
	 */
	public function clearFlags(): static
	{
		$this->flags = [];
		return $this;
	}

	/**
	 * @return array $columns
	 */
	public function getColumns(): array
	{
		return $this->columns;
	}

	/**
	 * Returns array | string order.
	 * @return string | array
	 */
	public function getOrder(): array|string
	{
		return $this->order;
	}
	
	/**
	 * @return array | string $groupBy
	 */
	public function getGroupBy(): array|string
	{
		return $this->groupBy;
	}

	/**
	 * @return Pager | int $limit
	 */
	public function getLimit(): Pager|int|null
	{
		return $this->limit ?? null;
	}

	/**
	 * @return int $limitOffset
	 */
	public function getLimitOffset(): int
	{
		return $this->limitOffset;
	}

	/**
	 * Set the table for the select query. This can be another Select object or a Union object. However, a table
	 * alias is required when using another object.
	 * @param Query|Union|array|string $table
	 * @param string|null $alias
	 * @return $this
	 * @throws QueryException
	 */
	public function setTable(Query|Union|array|string $table, string $alias = NULL): Select
	{
		if(is_array($table))
		{
			$this->table = $table;
		}
		elseif($table instanceof Query || $table instanceof Union)
		{
			//Check for a derived table alias
			if(!isset($alias))
				throw new QueryException('Every derived table must have its own alias', Error::DB_ERROR);

			//Remove the order by clause in the derived table.
			$table->setOrder(null);

			//Set the table.
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
     * @param array $columns
     * @return $this
	 */
	public function setColumns(array $columns): static
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
     * @return $this
	 */
	public function setOrder($order): static
	{
		$this->order = $order;
		return $this;
	}
	
	/**
	 * @param string | array $groupBy
     * @return $this
	 */
	public function setGroupBy($groupBy): static
	{
		$this->groupBy = $groupBy;
		return $this;
	}

	/**
	 * @param int $limit
	 * @return Select
	 */
	public function setLimit($limit): static
	{
		$this->limit = (int)$limit;
		return $this;
	}
	
	/**
	 * @param int $limitOffset
	 * @return Select
	 */
	public function setLimitOffset($limitOffset): static
	{
		$this->limitOffset = (int)$limitOffset;
		return $this;
	}

	/**
	 * Add to the list of columns. Optional parameter to name a column.
	 * @param string | Select $col
	 * @param string|null $name
	 * @return $this
	 */
	public function addColumn(string|Select $col, string|null $name = NULL): static
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
     * @return $this;
	 */
	public function addColumnsArray(array $columns): static
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
	 * @param array $columns
	 * @return Select
	 */
	public function columns(array $columns): static
	{
		return $this->setColumns($columns);
	}
	
	/**
	 * Remove a column from the $columns array.
	 * @param string $col
     * @return bool
	 */
	public function removeColumn(string $col): bool
	{
		if(($key = array_search($col, $this->columns)) !== false)
		{
			unset($this->columns[$key]);
			return true;
		}
		return false;
	}
	
	/**
	 * Remove a column from the $columns property by its alias.
	 * @param string $name
     * @return bool
	 */
	public function removeColumnByName(string $name): bool
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
     * @param array|string $order
     * @return $this
	 */
	public function orderBy(array|string $order): static
	{
		return $this->setOrder($order);
	}
	
	/**
	 * Alias of setGroupBy()
	 * @param string | array $group
	 * @see self::setGroupBy()
     * @return self::setGroupBy()
	 */
	public function groupBy($group)
	{
		return $this->setGroupBy($group);
	}
	
	/**
	 * Sets the limit and the offset in one function.
	 * @param int | Pager $limit
	 * @param int|null $offset
	 * @return $this
	 */
	public function limit(Pager|int $limit, int|null $offset = NULL): static
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

	/**
	 * Alias of setLimitOffset() method
	 * @param $offset
	 * @return $this
	 */
	public function skip($offset): static
	{
		return $this->setLimitOffset($offset);
	}

	/**
	 * Alias of setLimit() method
	 * @param $amount
	 * @return $this
	 */
	public function take($amount): static
	{
		return $this->setLimit($amount);
	}

	/*-----------------------------------------------HAVING CLAUSES-----------------------------------------------*/
	
	public function addHaving(Condition $having): static
	{
		$this->having[] = $having;
		return $this;
	}
	
	public function clearHaving(): static
	{
		$this->having = array();
		return $this;
	}

	/**
	 * Add A HAVING clause to the SELECT statement using the Condition object
	 * @param string $column
	 * @param string $operator
	 * @param mixed $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return $this
	 */
	public function havingCondition(string $column, string $operator, mixed $value, bool|null $columnJoin = null, string $paramName = null, bool $parameterized = true): static
	{
		$this->addHaving(Condition::get($column, $operator, $value, $columnJoin, $paramName, Condition::SQL_AND, $parameterized));
		return $this;
	}

	/**
	 * Add A raw HAVING clause to the SELECT statement
	 * @param string|Condition $statement
	 * @return $this
	 */
	public function havingStatement(string|Condition $statement): static
	{
		$this->addHaving(Condition::statement($statement));
		return $this;
	}

	/**
	 * Add A HAVING EQUAL clause to the SELECT statement
	 * @param string $column
	 * @param mixed $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return $this
	 */
	public function havingEqual(string $column, mixed $value, bool|null $columnJoin = null, string $paramName = null, bool $parameterized = true): static
	{
		$this->addHaving(Condition::equal($column, $value, $columnJoin, $paramName, Condition::SQL_AND, $parameterized));
		return $this;
	}

	/**
	 * Add A HAVING LIKE clause to the SELECT statement
	 * @param string $column
	 * @param mixed $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return $this
	 */
	public function havingLike(string $column, mixed $value, bool|null $columnJoin = null, string $paramName = null, bool $parameterized = true): static
	{
		$this->addHaving(Condition::like($column, $value, $columnJoin, $paramName, Condition::SQL_AND, $parameterized));
		return $this;
	}

	/**
	 * Add A HAVING NULL clause to the SELECT statement
	 * @param string $column
	 * @return $this
	 */
	public function havingNull(string $column): static
	{
		$this->addHaving(Condition::null($column));
		return $this;
	}

	/**
	 * Add A HAVING IN clause to the SELECT statement
	 * @param string $column
	 * @param array $values
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return $this
	 */
	public function havingIn(string $column, array $values, string $paramName = null, bool $parameterized = true): static
	{
		$this->addHaving(Condition::in($column, $values, $paramName, Condition::SQL_AND, $parameterized));
		return $this;
	}

	/**
	 * Add A HAVING BETWEEN clause to the SELECT statement
	 * @param string $column
	 * @param mixed $start
	 * @param mixed $end
	 * @param string|null $startParamName
	 * @param string|null $endParamName
	 * @param bool $parameterized
	 * @return $this
	 * @throws QueryException
	 */
	public function havingBetween(string $column, mixed $start, mixed $end, string $startParamName = null, string $endParamName = null, bool $parameterized = true): static
	{
		$this->addHaving(Condition::between($column, $start, $end, $startParamName, $endParamName, Condition::SQL_AND, $parameterized));
		return $this;
	}
	

	/*-----------------------------------------------JOIN FUNCTIONS-----------------------------------------------*/
	/**
	 * Add a join to the query.
	 * @param Join $join
	 * @return $this
	 */
	public function addJoin(Join $join): static
	{
		$join->setParentQuery($this);
		$this->joins[] = $join;
		return $this;
	}
	
	/**
	 * Remove a join from the query by table name.
	 * @param string $table
	 * @return boolean
	 */
	public function removeJoin(string $table): bool
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
	 * Returns true if a specified table is already joined to the query, false otherwise.
	 * @param string $table
	 * @return boolean
	 */
	public function isJoined(string $table): bool
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

	/**
	 * Join to another table using an outer join.
	 * @param string $table
	 * @param string $condition
	 * @param string|null $alias
	 * @param string|null $schema
	 * @return $this
	 */
	public function leftJoin(string $table, string $condition, string|null $alias = NULL, string|null $schema = NULL): static
	{
		$this->addJoin(Join::left($table, $condition, $alias, $schema));
		return $this;
	}

	/**
	 * Join to another table using an inner join.
	 * @param string $table
	 * @param string $condition
	 * @param string|null $alias
	 * @param string|null $schema
	 * @return $this
	 */
	public function innerJoin(string $table, string $condition, string|null $alias = NULL, string|null $schema = NULL): static
	{
		$this->addJoin(Join::inner($table, $condition, $alias, $schema));
		return $this;
	}

	/**
	 * Join to another table using an inner join.
	 * @param string $table
	 * @param string $condition
	 * @param string|null $alias
	 * @param string|null $schema
	 * @return $this
	 */
	public function join(string $table, string $condition, string|null $alias = NULL, string|null $schema = null): static
	{
		$this->addJoin(Join::inner($table, $condition, $alias, $schema));
		return $this;
	}
	
	/**
	 * Returns the "joins" array
	 * @return Join[]
	 */
	public function getJoins(): array
	{
		return $this->joins;
	}
	
	/*-----------------------------------------------BUILD FUNCTION-----------------------------------------------*/
	
	/**
	 * Builds the query and returns the string.
	 * @see Staple_Query::build()
	 * @param bool $parameterized
	 * @return string
	 */
	function build(bool $parameterized = null): string
	{
		if(isset($parameterized))
			$this->setParameterized($parameterized);

		$stmt = 'SELECT';

		//Flags
		if(count($this->flags) > 0)
		{
			$stmt .= ' '.implode(' ', $this->flags);
		}

		//SQL Server Limit - when offset is zero
		if($this->getLimit() > 0
			&& $this->getLimitOffset() == 0
			&& $this->getConnection()->getDriver() == Connection::DRIVER_SQLSRV)
		{
			$stmt .= ' TOP ' . $this->getLimit().' ';
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
					switch($this->getConnection()->getDriver())
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
					$tables	.= '('.$tbl.')';
				}
				else 
				{
					if(isset($this->schema))
					{
						$stmt .= $this->schema.'.';
					}
					elseif(!empty($this->connection->getSchema()))
					{
						$stmt .= $this->connection->getSchema().'.';
					}
					$tables .= $tbl;
				}

				//Add table aliases where applicable
				if(is_string($name))
				{
					//Switch the method based on database driver of the current connection
					switch($this->getConnection()->getDriver())
					{
						case Connection::DRIVER_MYSQL:
							$tables .= " AS `$name`";
							break;
						default:
							$tables .= " AS $name";
					}
				}
			}
			$stmt .= $tables;
		}
		elseif($this->table instanceof Query)
		{
			$stmt .= (string)$this->table;
		}
		else
		{
			if(isset($this->schema))
			{
				$stmt .= $this->schema.'.';
			}
			elseif(!empty($this->connection->getSchema()))
			{
				$stmt .= $this->connection->getSchema().'.';
			}
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

			//SQL Server Pagination
			if($this->getConnection()->getDriver() == Connection::DRIVER_SQLSRV)
			{
				if (isset($this->limit) && $this->getLimitOffset() != 0)
				{
					//Offset
					$stmt .= "\nOFFSET " . $this->getLimitOffset(). ' ROWS ';

					//Limit
					$stmt .= ' FETCH NEXT ' . $this->getLimit(). ' ROWS ONLY ';
				}
			}
		}

		//Limit clause is MySQL specific
		if($this->getConnection()->getDriver() == Connection::DRIVER_MYSQL)
		{
			//LIMIT CLAUSE
			if (isset($this->limit))
			{
				$stmt .= "\nLIMIT " . $this->getLimit();
				if (isset($this->limitOffset))
				{
					$stmt .= ' OFFSET ' . $this->limitOffset;
				}
			}
		}
		return $stmt;
	}
}