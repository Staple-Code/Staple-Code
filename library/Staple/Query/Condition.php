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
	 * The name of the parameter for a parameterized query.
	 * @var string
	 */
	protected $paramName;
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
					foreach($this->value as $aValue)
					{
						if(strlen($value) > 1)
						{
							$value .= ",";
						}
						$value .= $this->columnJoin ? $aValue : Query::convertTypes($aValue,$connection);
					}
				}
				elseif($this->value instanceof Select)
				{
					$value .= $this->value;
				}
				else
				{
					$value = $this->columnJoin ? $this->value : Query::convertTypes($this->value,$connection);
				}
				$value .= ")";
			}
			elseif(strtoupper($this->operator) === self::BETWEEN || $this->columnJoin === true)
			{
				if($this->parameterized)
				{
					if(strlen($this->paramName) > 0)
						$value = $this->getParamName();
					else
						$value = '?';
				}
				else
				{
					$value = $this->getValue();
				}
			}
			else
			{
				if($this->parameterized)
				{
					if(strlen($this->paramName) > 0)
						$value = $this->getParamName();
					else
						$value = '?';
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
	public function getParamName(): string
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
	 * @return bool
	 */
	public function parameterized(): bool
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
	 * @param $column
	 * @param $operator
	 * @param $value
	 * @param bool $columnJoin
	 * @return static
	 */
	public static function get($column, $operator, $value, $columnJoin = NULL)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator($operator)
			->setValue($value);
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
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
	 * @param string $paramName
	 * @param bool $columnJoin
	 * @param bool $parameterized
	 * @return Condition
	 */
	public static function equal($column, $value, string $paramName = null, $columnJoin = NULL, $parameterized = true)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setValue($value)
			->setParameterized($parameterized);
		
		//Check for NULLS
		is_null($value) ? $obj->setOperator(self::IS) :	$obj->setOperator(self::EQUAL);

		if(is_null($paramName))
			$obj->setParamName(":".$column);
		
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}

	/**
	 * Setup a SQL WHERE clause where a column is not equal to a value.
	 * @param string $column
	 * @param mixed $value
	 * @param bool $columnJoin
	 * @return Condition
	 */
	public static function notEqual($column, $value, $columnJoin = NULL)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setValue($value);

		//Check for NULLS
		is_null($value) ? $obj->setOperator(self::IS_NOT) :	$obj->setOperator(self::NOTEQUAL);

		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool $columnJoin
	 * @return static
	 */
	public static function like($column, $value, $columnJoin = NULL)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::LIKE)
			->setValue($value);
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool $columnJoin
	 * @return static
	 */
	public static function notLike($column, $value, $columnJoin = NULL)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::NOTLIKE)
			->setValue($value);
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}

	/**
	 * @param $column
	 * @return static
	 */
	public static function null($column)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::IS)
			->setValue(NULL);
		return $obj;
	}

	/**
	 * @param $column
	 * @param $values
	 * @param bool $columnJoin
	 * @return static
	 */
	public static function in($column, $values, $columnJoin = NULL)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::IN)
			->setValue($values);
		if(isset($columnJoin)) 
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}

	/**
	 * @param $column
	 * @param $start
	 * @param $end
	 * @param IConnection $connection
	 * @return static
	 * @throws QueryException
	 */
	public static function between($column, $start, $end, IConnection $connection = NULL)
	{
		/** @var Condition $obj */
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::BETWEEN)
			->setValue(Query::convertTypes($start)." AND ".Query::convertTypes($end))
			->setColumnJoin(true);
		
		if(isset($connection))
			$obj->setConnection($connection);
		
		return $obj;
	}
}