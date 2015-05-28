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

use \Exception;
use \Staple\Error;
use \Staple\Pager;

class Update extends Query
{
	const LOW_PRIORITY = 'LOW_PRIORITY';
	const IGNORE = 'IGNORE';
	
	/**
	 * An array of flags to apply to the query
	 * @var array[string]
	 */
	protected $flags = array();
	/**
	 * The data with which to update.
	 * @var DataSet[]
	 */
	public $data = array();
	/**
	 * Holds the order of the SQL query. It can be either a string or an array of the columns to order by.
	 * @var string | array
	 */
	protected $order;
	/**
	 * Limit number of rows to return.
	 * @var Pager | int
	 */
	protected $limit;
	/**
	 * The Limit Offset. Used to skip a number of rows before selecting.
	 * @var int
	 */
	protected $limitOffset;

	/**
	 * @param string $table
	 * @param array $data
	 * @param Connection $db
	 * @param array | string $order
	 * @param Pager | int $limit
	 * @throws Exception
	 */
	public function __construct($table = NULL, array $data = NULL, Connection $db = NULL, $order = NULL, $limit = NULL)
	{
		$this->data = new DataSet();
		if(isset($db))
		{
			$this->setConnection($db);
		}
		if(isset($table))
		{
			$this->setTable($table);
		}
		if(isset($data))
		{
			$this->setData($data);
		}
		if(isset($order))
		{
			$this->orderBy($order);
		}
		if(isset($limit))
		{
			$this->limit($limit);
		}
	}
	
	public function addFlag($flag)
	{
		switch($flag)
		{
			case self::LOW_PRIORITY:
			case self::IGNORE:
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
	 * Adds or replaces data in the insert dataset.
	 * @param array $data
	 * @throws Exception
	 */	
	public function addData(array $data)
	{
		$this->data->addData($data);
		return $this;
	}
	
	/**
	 * Adds or replaces a specific column value. Alias is set Data Column
	 * @param string $column
	 * @param mixed $data
	 * @throws Exception
	 * @see self::setDataColumn
	 */
	public function addDataColumn($column, $data)
	{
		return $this->setDataColumn($column, $data);
	}
	
	/**
	 * Adds a literal value to the dataset without conversion.
	 * @param string $column
	 * @param string $value
	 */
	public function addLiteralColumn($column, $value)
	{
		return $this->setDataColumn($column, $value, true);
	}

	//----------------------------------------------GETTERS AND SETTERS----------------------------------------------
	
	/**
	 * @return DataSet[] $columns
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * Returns the order.
	 * @return string | array
	 */
	public function getOrder()
	{
		return $this->order;
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
	 * Set the order.
	 * @param string | array $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
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
	 * Sets the $data
	 * @param DataSet
	 * @return $this
	 */
	public function setData($data)
	{
		if($data instanceof DataSet)
		{
			$this->data = $data;
		}
		elseif(is_array($data))
		{
			$this->data = new DataSet($data);
		}
		else
		{
			throw new Exception('Data must be an instance of Staple_Query_DataSet or an array', Error::APPLICATION_ERROR);
		}
		return $this;
	}
	
	/**
	 * Sets the specified value for a specific column.
	 * @param string $column
	 * @param mixed $data
	 * @param bool $literal
	 * @throws Exception
	 * @return $this
	 */
	public function setDataColumn($column,$data,$literal = false)
	{
		if($literal === true)
		{
			$this->data->addLiteralColumn($column, $data);
		}
		else
		{
			$this->data[$column] = $data;
		}
		return $this;
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

	/**
	 * @param int $limitOffset
	 * @return Select
	 */
	public function setLimitOffset($limitOffset)
	{
		$this->limitOffset = (int)$limitOffset;
		return $this;
	}
	
	/*-----------------------------------------------BUILD FUNCTION-----------------------------------------------*/
	
	/**
	 * 
	 * @see Staple_Query::build()
	 */
	function build()
	{
		$stmt = 'UPDATE ';
		
		//Flags
		if(count($this->flags) > 0)
		{
			$stmt .= ' '.implode(' ', $this->flags);
		}
		
		//Table
		if(is_array($this->table))
		{
			$stmt .= ' ';			//A little extra space
			foreach($this->table as $alias=>$table)
			{
				$stmt .= $table;
				if(is_string($alias))
				{
					$stmt .= ' AS `'.$alias.'`';
				}
			}
		}
		else 
		{
			$stmt .= ' '.$this->table;
		}
		
		//SET data
		if(count($this->data) >= 0)
		{
			$stmt .= "\nSET ";
			$stmt .= $this->data->getUpdateString();
		}
		
		//WHERE CLAUSE
		if(count($this->where) > 0)
		{
			$stmt .= "\nWHERE ".implode(' AND ', $this->where);
		}
		
		//Can only order and limit on a single table query
		if(!is_array($this->table))
		{
			//ORDER CLAUSE
			if(isset($this->order))
			{
				$stmt .= "\nORDER BY ";
				if(is_array($this->order))
				{
					$stmt .= implode(',', $this->order);
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
		}
		return $stmt;
	}
}