<?php

/** 
 * A class to contain SQL condional statements.
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

use Exception;
use Staple\Exception\ConfigurationException;
use Staple\Exception\QueryException;

class Condition
{
	const EQUAL = '=';
	const GREATER = '>';
	const GREATER_EQUAL = '>=';
	const LESS = '<';
	const LESS_EQUAL = '<=';
	const NOTEQUAL = '<>';
	const IN = "IN";
	const IS = "IS";
	const IS_NOT = "IS NOT";
	const BETWEEN = "BETWEEN";
	const LIKE = "LIKE";
	const NOTLIKE = "NOT LIKE";
	const PARAMETERIZED_QUERY = true;
	const NON_PARAMETERIZED_QUERY = false;
	const SQL_AND = 'AND';
	const SQL_OR = 'OR';

	/**
	 * The column for the where
	 * @var string
	 */
	protected $column;
	/**
	 * The operator of the where clause
	 * @var string
	 */
	protected $operator;
	/**
	 * The value of the comparison
	 * @var mixed
	 */
	protected $value;
	/**
	 * An array of values used for IN and BETWEEN operators
	 * @var array
	 */
	protected $valueArray;
	/**
	 * The name of the parameter for a parameterized query.
	 * @var string
	 */
	protected $paramName;
	/**
	 * An array of parameter names used for the IN and BETWEEN operators
	 * @var string[]
	 */
	protected $paramArray;
	/**
	 * Flag to
	 * @var bool
	 */
	protected $parameterized = true;
	/**
	 * An override statement that represents the WHERE clause.
	 * @var string
	 */
	public $statement;
	/**
	 * True or false whether the value is a table column.
	 * @var bool
	 */
	protected $columnJoin = false;

	/**
	 * SQL Conditional Conjunction (AND/OR)
	 * @var string
	 */
	protected $conjunction;
	/**
	 * Reference to the connection object in use for this clause.
	 * @var Connection
	 */
	protected $connection;

	/**
	 * @param string $statement
	 * @param Connection $connection
	 */
	public function __construct($statement = NULL, Connection $connection = NULL)
	{
		if(isset($statement))
			$this->setStatement($statement);
		if(isset($connection))
			$this->setConnection($connection);
	}
	
	/**
	 * Returns the string of the where clause.
	 * @return string 
	 */
	public function __toString()
	{
		try {
			$return = $this->build($this->getConnection());
		}
		catch (Exception $e)
		{
			//on an error return SQL that eliminates any results from the query.
			return "1 = 2";
		}
		return $return;
	}
	
	/**
	 * Sets the where statement.
	 * @param string $where
	 * @return $this
	 */
	public function setWhere($where)
	{
		$this->statement = (string)$where;
		return $this;
	}

	/**
	 * @param Connection $connection
	 * @return string
	 * @throws QueryException
	 */
	public function build(Connection $connection)
	{
		if(isset($this->statement))
		{
			return '('.$this->statement.')';
		}
		else
		{
			if(strtoupper($this->operator) == self::IN)
			{
				$value = "(";

				if(is_array($this->value))
				{
					if($this->isParameterized())
					{
						$count = 0;
						foreach($this->value as $aValue)
						{
							$count++;
							$value .=
								$this->columnJoin ?
									$aValue :
									(strlen($this->paramName) > 0) ?
										':' . $this->getParamName() . '_in_' . $count  :
										'?';
						}
					}
					else
					{
						foreach($this->value as $aValue)
						{
							if(strlen($value) > 1)
							{
								$value .= ", ";
							}
							$value .= $this->columnJoin ? $aValue : Query::convertTypes($aValue, $connection);
						}
					}
				}
				elseif($this->value instanceof Select)
				{
					$value .= $this->value;
				}
				else
				{
					if($this->isParameterized())
					{
						$value .= (strlen($this->paramName) > 0) ? ':' . $this->getParamName() : '?';
					}
					else
					{
						$value .= $this->columnJoin ? $this->value : Query::convertTypes($this->value, $connection);
					}
				}
				$value .= ")";
			}
			elseif(strtoupper($this->operator) === self::BETWEEN)
			{
				if($this->isParameterized())
				{
					if(strlen($this->paramArray['start']) > 0 && strlen($this->paramArray['end']) > 0)
					{
						$value = ':' . $this->paramArray['start'] . ' AND :'. $this->paramArray['end'];
					}
					else
					{
						$value = '? AND ?';
					}
				}
				else
				{
					$value = $this->getValue();
				}
			}
			//Use the column value in th
			elseif($this->columnJoin === true)
			{
				$value = $this->getValue();
			}
			//Don't parameterize a NULL value
			elseif((strtoupper($this->operator) === self::IS || strtoupper($this->operator) === self::IS_NOT) && is_null($this->value))
			{
				$value = Query::convertTypes($this->value, $connection);
			}
			else
			{
				if($this->isParameterized())
				{
					if(strlen($this->paramName) > 0)
					{
						$value = ':' . $this->getParamName();
					}
					else
					{
						$value = '?';
					}
				}
				else
				{
					$value = Query::convertTypes($this->value, $connection);
				}
			}
			return $this->column.' '.$this->operator.' '.$value;
		}
	}
	
	/*-----------------------------------------------GETTERS AND SETTERS-----------------------------------------------*/
	
	/**
	 * @return string $column
	 */
	public function getColumn()
	{
		return $this->column;
	}

	/**
	 * @return string $operator
	 */
	public function getOperator()
	{
		return $this->operator;
	}

	/**
	 * @return mixed $value
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string $statement
	 */
	public function getStatement()
	{
		return $this->statement;
	}

	/**
	 * @param string $column
	 * @return $this
	 */
	public function setColumn($column)
	{
		$this->column = $column;
		return $this;
	}

	/**
	 * @param string $operator
	 * @return $this
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
		return $this;
	}

	/**
	 * @param string $value
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * @param string $statement
	 * @return $this
	 */
	public function setStatement($statement)
	{
		$this->statement = $statement;
		return $this;
	}
	
	/**
	 * @return bool $columnJoin
	 */
	public function getColumnJoin()
	{
		return $this->columnJoin;
	}

	/**
	 * @param bool $columnJoin
	 * @return $this
	 */
	public function setColumnJoin(bool $columnJoin)
	{
		$this->columnJoin = $columnJoin;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getParamName(): ?string
	{
		return $this->paramName;
	}

	/**
	 * @param string $paramName
	 * @return Condition
	 */
	public function setParamName(string $paramName): Condition
	{
		$this->paramName = $paramName;
		return $this;
	}

	/**
	 * @param string $paramName
	 * @return Condition
	 */
	public function setStartParamName(string $paramName): Condition
	{
		$this->paramArray['start'] = $paramName;
		return $this;
	}

	/**
	 * @param string $paramName
	 * @return Condition
	 */
	public function setEndParamName(string $paramName): Condition
	{
		$this->paramArray['end'] = $paramName;
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
	 * @return Condition
	 */
	public function setParameterized(bool $parameterized): Condition
	{
		$this->parameterized = $parameterized;
		return $this;
	}

	/**
	 * Return the currently set connection or attempt to retrieve the default connection if non specified.
	 * @return Connection
	 * @throws ConfigurationException
	 */
	public function getConnection()
	{
		if(isset($this->connection))
			return $this->connection;
		else
			return Connection::get();
	}

	/**
	 * Set the connection.
	 * @param IConnection $connection
	 * @return $this
	 */
	public function setConnection(IConnection $connection)
	{
		$this->connection = $connection;
		return $this;
	}


	
	/*-----------------------------------------------CONDITION ENCAPSULATORS-----------------------------------------------*/

	/**
	 * @param string|Query $column
	 * @param $operator
	 * @param mixed $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param string $conjunction
	 * @param bool $parameterized
	 * @return static
	 */
	public static function get($column, $operator, $value, bool $columnJoin = NULL, string $paramName = null, $conjunction = self::SQL_AND, bool $parameterized = true)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator($operator)
			->setValue($value)
			->setParameterized($parameterized);

		//Query Parameter Name
		if(is_null($paramName))
			$obj->setParamName(Query::sanitizeParamName($column));
		else
			$obj->setParamName(Query::sanitizeParamName($paramName));

		//Column Join
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);

		//Where Conjunction
		$obj->conjunction = (strtoupper($conjunction) === self::SQL_OR) ? self::SQL_OR : self::SQL_AND;

		return $obj;
	}

	/**
	 * @param $statement
	 * @return static
	 */
	public static function statement($statement)
	{
		$class = new static();
		$class->setStatement($statement);
		return $class;
	}
	
	/**
	 * Setup a SQL WHERE clause where a column is equal to a value.
	 * @param string $column
	 * @param mixed $value
	 * @param bool $columnJoin
	 * @param string $paramName
	 * @param string $conjunction
	 * @param bool $parameterized
	 * @return Condition
	 */
	public static function equal($column, $value, bool $columnJoin = NULL, string $paramName = null, $conjunction = self::SQL_AND, bool $parameterized = true)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setValue($value)
			->setParameterized($parameterized);
		
		//Check for NULLS
		is_null($value) ? $obj->setOperator(self::IS) :	$obj->setOperator(self::EQUAL);

		//Query Parameter Name
		if(is_null($paramName))
			$obj->setParamName(Query::sanitizeParamName($column));
		else
			$obj->setParamName(Query::sanitizeParamName($paramName));

		//Column Join
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);

		//Where Conjunction
		$obj->conjunction = (strtoupper($conjunction) === self::SQL_OR) ? self::SQL_OR : self::SQL_AND;

		return $obj;
	}

	/**
	 * Setup a SQL WHERE clause where a column is not equal to a value.
	 * @param string $column
	 * @param mixed $value
	 * @param bool $columnJoin
	 * @param string $paramName
	 * @param string $conjunction
	 * @param bool $parameterized
	 * @return Condition
	 */
	public static function notEqual($column, $value, bool $columnJoin = NULL, string $paramName = null, $conjunction = self::SQL_AND, bool $parameterized = true)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setValue($value)
			->setParameterized($parameterized);

		//Check for NULLS
		is_null($value) ? $obj->setOperator(self::IS_NOT) :	$obj->setOperator(self::NOTEQUAL);

		//Query Parameter Name
		if(is_null($paramName))
			$obj->setParamName(Query::sanitizeParamName($column));
		else
			$obj->setParamName(Query::sanitizeParamName($paramName));

		//Column Join
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);

		//Where Conjunction
		$obj->conjunction = (strtoupper($conjunction) === self::SQL_OR) ? self::SQL_OR : self::SQL_AND;

		return $obj;
	}

	/**
	 * @param string $column
	 * @param mixed $value
	 * @param bool $columnJoin
	 * @param string $paramName
	 * @param string $conjunction
	 * @param bool $parameterized
	 * @return static
	 */
	public static function like($column, $value, $columnJoin = NULL, string $paramName = null, $conjunction = self::SQL_AND, bool $parameterized = true)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::LIKE)
			->setValue($value)
			->setParameterized($parameterized);

		//Query Parameter Name
		if(is_null($paramName))
			$obj->setParamName(Query::sanitizeParamName($column));
		else
			$obj->setParamName(Query::sanitizeParamName($paramName));

		//Column Join
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);

		//Where Conjunction
		$obj->conjunction = (strtoupper($conjunction) === self::SQL_OR) ? self::SQL_OR : self::SQL_AND;

		return $obj;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool $columnJoin
	 * @param string|null $paramName
	 * @param $conjunction
	 * @param bool $parameterized
	 * @return static
	 */
	public static function notLike($column, $value, bool $columnJoin = NULL, string $paramName = null, $conjunction = self::SQL_AND, bool $parameterized = true)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::NOTLIKE)
			->setValue($value)
			->setParameterized($parameterized);

		//Query Parameter Name
		if(is_null($paramName))
			$obj->setParamName(Query::sanitizeParamName($column));
		else
			$obj->setParamName(Query::sanitizeParamName($paramName));

		//Columns Join
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);

		//Where Conjunction
		$obj->conjunction = (strtoupper($conjunction) === self::SQL_OR) ? self::SQL_OR : self::SQL_AND;

		return $obj;
	}

	/**
	 * @param string|Query $column
	 * @param string $conjunction
	 * @return static
	 */
	public static function null($column, $conjunction = Condition::SQL_AND)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::IS)
			->setValue(NULL);

		//Where Conjunction
		$obj->conjunction = (strtoupper($conjunction) === self::SQL_OR) ? self::SQL_OR : self::SQL_AND;

		return $obj;
	}

	/**
	 * @param $column
	 * @param $values
	 * @param string|null $paramName
	 * @param string $conjunction
	 * @param bool $parameterized
	 * @return static
	 */
	public static function in($column, $values, string $paramName = null, $conjunction = self::SQL_AND, bool $parameterized = true)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::IN)
			->setValue($values)
			->setParameterized($parameterized);

		//Query Parameter Name
		if(is_null($paramName))
			$obj->setParamName(Query::sanitizeParamName($column));
		else
			$obj->setParamName(Query::sanitizeParamName($paramName));

		//Where Conjunction
		$obj->conjunction = (strtoupper($conjunction) === self::SQL_OR) ? self::SQL_OR : self::SQL_AND;

		return $obj;
	}

	/**
	 * @param $column
	 * @param mixed $start
	 * @param mixed $end
	 * @param string $startParamName
	 * @param string $endParamName
	 * @param string $conjunction
	 * @param bool $parameterized
	 * @return static
	 * @throws QueryException
	 */
	public static function between($column, $start, $end, string $startParamName = null, string $endParamName = null, $conjunction = self::SQL_AND, bool $parameterized = true)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::BETWEEN)
			->setValue(Query::convertTypes($start)." AND ".Query::convertTypes($end))
			->setColumnJoin(true)
			->setParameterized($parameterized);

		//Start Parameter Name
		if(is_null($startParamName))
			$obj->setStartParamName(Query::sanitizeParamName($column.'_start'));
		else
			$obj->setStartParamName(Query::sanitizeParamName($startParamName));

		//End Parameter Name
		if(is_null($endParamName))
			$obj->setEndParamName(Query::sanitizeParamName($column.'_end'));
		else
			$obj->setEndParamName(Query::sanitizeParamName($endParamName));

		//Where Conjunction
		$obj->conjunction = (strtoupper($conjunction) === self::SQL_OR) ? self::SQL_OR : self::SQL_AND;
		
		return $obj;
	}
}