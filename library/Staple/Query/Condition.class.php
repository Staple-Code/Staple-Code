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

use \Exception;

class Condition
{
	const EQUAL = '=';
	const GREATER = '>';
	const GREATER_EQUAL = '>=';
	const LESS = '<';
	const LESS_EQUAL = '<=';
	const NOTEQUAL = '!=';
	const IN = "IN";
	const IS = "IS";
	const BETWEEN = "BETWEEN";
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
	 * @var string | int | bool
	 */
	protected $value;
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
	
	public function __construct($statement = NULL)
	{
		if(isset($statement))
		{
			$this->setStatement($statement);
		}
	}
	
	/**
	 * Returns the string of the where clause.
	 * @return string 
	 */
	public function __toString()
	{
		try {
			$return = $this->build(); 
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
	 * @param unknown_type $where
	 */
	public function setWhere($where)
	{
		$this->statement = (string)$where;
		return $this;
	}
	
	public function build()
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
						$value .= $this->columnJoin ? $aValue : Query::convertTypes($aValue);
					}
				}
				elseif($this->value instanceof Select)
				{
					$value .= $this->value;
				}
				else
				{
					$value = $this->columnJoin ? $this->value : Query::convertTypes($this->value);
				}
				$value .= ")";
			}
			elseif (strtoupper($this->operator) == self::BETWEEN)
			{
				$value = $this->getValue();
			}
			else 
			{
				$value = $this->columnJoin ? $this->value : Query::convertTypes($this->value);
			}
			return $this->column.' '.$this->operator.' '.$value;
		}
	}
	
	/*-----------------------------------------------GETTERS AND SETTERS-----------------------------------------------*/
	
	/**
	 * @return the $column
	 */
	public function getColumn()
	{
		return $this->column;
	}

	/**
	 * @return the $operator
	 */
	public function getOperator()
	{
		return $this->operator;
	}

	/**
	 * @return the $value
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return the $statement
	 */
	public function getStatement()
	{
		return $this->statement;
	}

	/**
	 * @param string $column
	 */
	public function setColumn($column)
	{
		$this->column = $column;
		return $this;
	}

	/**
	 * @param string $operator
	 */
	public function setOperator($operator)
	{
		$this->operator = $operator;
		return $this;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * @param string $statement
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
	 */
	public function setColumnJoin($columnJoin)
	{
		$this->columnJoin = (bool)$columnJoin;
		return $this;
	}
	
	/*-----------------------------------------------CONDITION ENCAPSULATORS-----------------------------------------------*/

	public static function Get($column, $operator, $value, $columnJoin = NULL)
	{
		$obj = new static();
		$obj->setColumn($column)
			->setOperator($operator)
			->setValue($value);
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}
	
	public static function Statement($statement)
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
	 * @return Staple_Query_Condition
	 */
	public static function Equal($column, $value, $columnJoin = NULL)
	{
		$obj = new static();
		$obj->setColumn($column)
			->setValue($value);
		
		//Check for NULLS
		is_null($value) ? $obj->setOperator(self::IS) :	$obj->setOperator(self::EQUAL);
		
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}
	
	public static function Like($column, $value, $columnJoin = NULL)
	{
		$obj = new static();
		$obj->setColumn($column)
			->setOperator('LIKE')
			->setValue($value);
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}
	
	public static function Null($column)
	{
		$obj = new static();
		$obj->setColumn($column)
			->setOperator('IS')
			->setValue(NULL);
		return $obj;
	}
	
	public static function In($column, $values, $columnJoin = NULL)
	{
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::IN)
			->setValue($values);
		if(isset($columnJoin))
			$obj->setColumnJoin($columnJoin);
		return $obj;
	}
	
	public static function Between($column, $start, $end)
	{
		$obj = new static();
		$obj->setColumn($column)
			->setOperator(self::BETWEEN)
			->setValue(Query::convertTypes($start)." AND ".Query::convertTypes($end))
			->setColumnJoin(true);
		return $obj;
	}
}

?>