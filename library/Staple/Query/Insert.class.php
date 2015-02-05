<?php
/** 
 * A class for creating SQL INSERT statements.
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
 */
namespace Staple\Query;

use \Exception;
use \mysqli;
use \Staple\Error;
use \Staple\DB;

class Insert
{
	const LOW = "LOW_PRIORITY";
	const DELAYED = "DELAYED";
	const HIGH = "HIGH_PRIORITY";
	
	/**
	 * The database object. A database object is required to properly escape input.
	 * @var mysqli
	 */
	protected $db;
	/**
	 * The data to insert. May be a Select Statement Object or an array of DataSets
	 * @var Staple_Query_DataSet | Staple_Query_Select
	 */
	protected $data;
	/**
	 * The Priority parameter of the SQL statement
	 * @var string
	 */
	protected $priority;
	/**
	 * A boolean value used to set the IGNORE parameter
	 * @var boolean
	 */
	protected $ignore = false;
	/**
	 * Table to update.
	 * @var string
	 */
	protected $table;
	
	/**
	 * Boolean flag for ON DUPLICATE KEY UPDATE
	 * @var boolean
	 */
	protected $updateOnDuplicate = false;
	/**
	 * The columns to update on a duplicate key.
	 * @var array[string]
	 */
	protected $updateColumns = array();

	/**
	 * @param string $table
	 * @param array $data
	 * @param DB $db
	 * @param string $priority
	 * @throws Exception
	 */
	public function __construct($table = NULL, $data = NULL, $db = NULL, $priority = NULL)
	{
		$this->data = new DataSet();
		
		//Process Database connection
		if($db instanceof mysqli)
		{
			$this->setDb($db);
		}
		else
		{
			try {
				$this->setDb(DB::get());
			}
			catch (Exception $e)
			{
				$this->setDb(new mysqli());
			}
		}
		//No DB = Bad
		if(!($this->db instanceof mysqli))
		{
			throw new Exception('Unable to create database object', Error::DB_ERROR);
		}
		
		//Set Table
		if(isset($table))
		{
			$this->setTable($table);
		}
		
		//Set Data
		if(isset($data))
		{
			$this->setData($data);
		}
		
		//Set Priority
		if(isset($priority))
		{
			$this->setPriority($priority);
		}
	}
	
	/**
	 * Execute the build function and return the result when converting to a string.
	 */
	public function __toString()
	{
		try {
			$msg = $this->build();
		}
		catch (Exception $e)
		{
			$msg = $e->getMessage();
		}
		return $msg;
	}
	
	/**
	 * 
	 * @see Staple_Query::build()
	 */
	function build()
	{
		//Statement Start
		$stmt = "INSERT ";
		
		//Flags
		if(isset($this->priority))
		{
			$stmt .= $this->priority.' ';
		}
		if($this->ignore === TRUE)
		{
			$stmt .= 'IGNORE ';
		}
		
		//Table
		$stmt .= "\nINTO ".$this->table.' ';
		
		//Data
		if($this->data instanceof DataSet)
		{
			$stmt .= $this->data->getInsertString();
		}
		elseif($this->data instanceof Select)
		{
			$stmt .= "\n".$this->data;
		}
		
		//Duplicate Updates
		if($this->updateOnDuplicate === true)
		{
			$first = true;
			$stmt .= "\nON DUPLICATE KEY UPDATE ";
			foreach($this->updateColumns as $ucol)
			{
				if($first === true)
				{
					$first = false;
				}
				else
				{
					$stmt .= ',';
				}
				$stmt .= " $ucol=VALUES($ucol)";
			}
		}
		
		return $stmt;
	}
	
	/**
	 * Executes the query.
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
				$this->db = DB::get();
			}
			catch (Exception $e)
			{
				//@todo try for a default connection if no staple connection
				throw new Exception('No Database Connection', Error::DB_ERROR);
			}
			if($this->db instanceof mysqli)
			{
				return $this->db->query($this->build());
			}
		}
		return false;
	}
	
	
	/**
	 * Adds or replaces data in the insert dataset.
	 * @param array $data
	 * @throws Exception
	 */	
	public function addData(array $data)
	{
		if($this->data instanceof Select)
		{
			throw new Exception('Cannot add data to an INSERT ... SELECT statement.', Error::DB_ERROR);
		}
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
	 * @return the $db
	 */
	public function getDb()
	{
		return $this->db;
	}
	
	/**
	 * @return the $data
	 */
	public function getData()
	{
		return $this->data;
	}
	
	/**
	 * @return the $priority
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @return the $ignore
	 */
	public function getIgnore()
	{
		return $this->ignore;
	}

	/**
	 * @return the $table
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @return the $updateOnDuplicate
	 */
	public function getUpdateOnDuplicate()
	{
		return $this->updateOnDuplicate;
	}

	/**
	 * @return the $updateColumns
	 */
	public function getUpdateColumns()
	{
		return $this->updateColumns;
	}
	
	/**
	 * @param mysqli $db
	 */
	public function setDb(mysqli $db)
	{
		$this->db = $db;
		return $this;
	}
	
	/**
	 * Sets the $data
	 * @param Staple_Query_Select | Staple_Query_DataSet | array $data
	 */
	public function setData($data)
	{
		if($data instanceof Select || $data instanceof DataSet)
		{
			$this->data = $data;
		}
		elseif(is_array($data))
		{
			$this->data = new DataSet($data);
		}
		else
		{
			throw new Exception('Data must be an instance of Staple_Query_DataSet, an instance of Staple_Query_Select or an array', Error::APPLICATION_ERROR);
		}
		return $this;
	}
	
	/**
	 * Sets the specified value for a specific column.
	 * @param string $column
	 * @param mixed $data
	 * @param bool $literal
	 * @throws Exception
	 */
	public function setDataColumn($column,$data,$literal = false)
	{
		if($this->data instanceof Select)
		{
			throw new Exception('Cannot add data to an INSERT ... SELECT statement.', Error::DB_ERROR);
		}
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
	 * @param string $priority
	 */
	public function setPriority($priority)
	{
		switch($priority)
		{
			case self::DELAYED:
				$this->priority = self::DELAYED;
				break;
			case self::HIGH:
				$this->priority = self::HIGH;
			case self::LOW:
				$this->priority = self::LOW;
				break;
			default: $this->priority = NULL;
		}
		return $this;
	}

	/**
	 * @param boolean $ignore
	 */
	public function setIgnore($ignore)
	{
		$this->ignore = (bool)$ignore;
		return $this;
	}

	/**
	 * @param string $table
	 */
	public function setTable($table)
	{
		$this->table = $table;
		return $this;
	}

	/**
	 * @param bool $updateOnDuplicate
	 */
	public function setUpdateOnDuplicate($updateOnDuplicate)
	{
		$this->updateOnDuplicate = (bool)$updateOnDuplicate;
		return $this;
	}

	/**
	 * @param array[string] $updateColumns
	 */
	public function setUpdateColumns(array $updateColumns)
	{
		$this->updateColumns = $updateColumns;
		return $this;
	}
	
	/**
	 * Setup On Duplicate Key Update Syntax
	 * @param bool $bool
	 */
	public function onDuplicateKeyUpdate($bool = true)
	{
		$this->setUpdateOnDuplicate((bool)$bool);
		return $this;
	}

}

?>