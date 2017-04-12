<?php
/** 
 * A class for working with sets of data in SQL.
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

class DataSet implements \ArrayAccess, \Iterator, \Countable
{
	/**
	 * Array of data contained in the set
	 * @var array
	 */
	protected $data = array();
	/**
	 * Literal data in the set
	 * @var array
	 */
	protected $literal = array();
	/**
	 * The database connection
	 * @var IConnection
	 */
	protected $connection;

	/**
	 * @param array $data
	 * @param IConnection $connection
	 */
	public function __construct(array $data = NULL, IConnection $connection = NULL)
	{
		if(isset($data))
		{
			$this->setData($data);
		}
		if(isset($connection))
			$this->setConnection($connection);
	}

	/**
	 * Return the currently set connection or attempt to retrieve the default connection if non specified.
	 * @return IConnection
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
	
	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->data);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{
		if(array_key_exists($offset, $this->data))
		{
			return $this->data[$offset];
		}
		else
		{
			return NULL;
		}
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
		$this->literal[$offset] = false;
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
		unset($this->literal[$offset]);
	}
	/* (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current()
	{
		return current($this->data);
	}

	/* (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key()
	{
		return key($this->data);
	}

	/* (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next()
	{
		return next($this->data);
	}

	/* (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		return reset($this->data);
	}

	/* (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid()
	{
		if(key($this->data) === NULL)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/* (non-PHPdoc)
	 * @see Countable::count()
	 */
	public function count()
	{
		return count($this->data);
	}

	/**
	 * Adds a data pair (key and value) to the dataset. Will overwrite the value of any existing duplicate key.
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function addDataPair($key,$value)
	{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Appends the contents of the submitted array to the dataset.
	 * Note: numeric keys will be renumbered if duplicated, and string keys will be overwritten
	 * if duplicated.
	 *  
	 * @param array $data
	 * @return $this
	 */
	public function addData(array $data)
	{
		foreach ($data as $key=>$value)
		{
			$this->literal[$key] = false;
		}
		$this->data = array_merge($this->data, $data);
		return $this;
	}
	
	/**
	 * Clears any current data and uses the supplied data array.
	 * 
	 * @param array $data
	 * @return $this
	 */
	public function setData(array $data)
	{
		foreach ($data as $key=>$value)
		{
			$this->literal[$key] = false;
		}
		$this->data = $data;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return array
	 */
	public function getColumns()
	{
		return array_keys($this->data);
	}

	/**
	 * @param $column
	 * @return mixed|null
	 */
	public function getLiteralFor($column)
	{
		if(array_key_exists($column, $this->literal))
		{
			return $this->literal[$column];
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * @param $column
	 * @param $data
	 * @return $this
	 */
	public function addLiteralColumn($column, $data)
	{
		$this->data[$column] = $data;
		$this->literal[$column] = true;
		return $this;
	}

	/**
	 * @param array $data
	 * @return $this
	 */
	public function addLiteralData(array $data)
	{
		foreach ($data as $key=>$value)
		{
			$this->literal[$key] = true;
		}
		$this->data = array_merge($this->data, $data);
		return $this;
	}

	/**
	 * Get the INSERT string for the data in this object.
	 * @return string
	 */
	public function getInsertString()
	{
		$stmt = '('.implode(',',$this->getColumns()).') ';
		$stmt .= "\nVALUES (";
		$colCount = 0;
		foreach ($this->data as $name=>$col)
		{
			if($colCount > 0)
			{
				$stmt .= ',';
			}
			if($this->literal[$name] === true)
			{
				$stmt .= $col;
			}
			else
			{
				$stmt .= Query::convertTypes($col,$this->getConnection());
			}
			$colCount++;
		}
		$stmt .= ") ";
		return $stmt;
	}

	/**
	 * Get the multi-insert string for the data in this object.
	 * @return string
	 */
	public function getInsertMultipleString()
	{
		$stmt = '(';
		$colCount = 0;
		foreach ($this->data as $name=>$col)
		{
			if($colCount > 0)
			{
				$stmt .= ',';
			}
			if($this->literal[$name] === true)
			{
				$stmt .= $col;
			}
			else
			{
				$stmt .= Query::convertTypes($col,$this->getConnection());
			}
			$colCount++;
		}
		$stmt .= ")";
		return $stmt;
	}

	/**
	 * Get the UPDATE string for the data in this object.
	 * @return string
	 */
	public function getUpdateString()
	{
		$stmt = '';
		$colCount = 0;
		foreach ($this->data as $name=>$col)
		{
			if($colCount > 0)
			{
				$stmt .= ',';
			}
			$stmt .= ' '.$name.'=';
			if($this->literal[$name] === true)
			{
				$stmt .= $col;
			}
			else
			{
				$stmt .= Query::convertTypes($col,$this->getConnection());
			}
			$colCount++;
		}
		return $stmt;
	}
}