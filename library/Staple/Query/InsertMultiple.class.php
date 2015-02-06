<?php
/**
 * A class for creating SQL Multi-INSERT statements.
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
use \mysqli;
use \Staple\Error;
use \Staple\DB;

class InsertMultiple extends Insert
{
	/**
	 * The data to insert. May be a Select Statement Object or an array of DataSets
	 * @var array[Staple_Query_DataSet]
	 */
	protected $data = array();
	/**
	 * The columns to insert the data into
	 * @var array[string]
	 */
	protected $columns = array();
	
	/**
	 * Query to insert multiple rows
	 * @param string $table
	 * @param array $columns
	 * @param mysqli $db
	 * @param unknown_type $priority
	 * @throws Exception
	 */
	public function __construct($table = NULL, array $columns = NULL, $db = NULL, $priority = NULL)
	{	
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
		if(isset($columns))
		{
			$this->setColumns($columns);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Staple_Query_Insert::build()
	 */
	public function build()
	{
		//Statement start
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
		
		//DB Table
		$stmt .= "\nINTO ".$this->table.' ';
		
		//Columns
		$stmt .= '('.implode(',', $this->columns).') ';
		
		//Data
		$stmt .= 'VALUES ';
		$rows = array();
		foreach($this->data as $dataset)
		{
			if($dataset instanceof DataSet)
			{
				$rows[] = trim($dataset->getInsertMultipleString());
			}
			else
			{
				//Return null string since we can't throw exceptions on __toString().
				return '';
			}
		}
		$stmt .= implode(', ', $rows);
		
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
	 * Adds a dataset to the object
	 * @param Staple_Query_DataSet $row
	 */
	public function addRow(DataSet $row)
	{
		if(count($row->getColumns()) != count($this->columns))
		{
			throw new Exception('DataSet row count does not match the count for this object');
		}
		else
		{
			$this->data[] = $row;
			return $this;
		}
	}
	
	//------------------------------------------------GETTERS AND SETTERS------------------------------------------------//
	
	/**
	 * @return the $columns
	 */
	public function getColumns()
	{
		return $this->columns;
	}
	/**
	 * @param array[string] $columns
	 */
	public function setColumns(array $columns)
	{
		$this->columns = $columns;
		return $this;
	}
	
	/**
	 * Overides the original setter to verify that the dataset is inserted with proper specifications. 
	 * @param array[Staple_Query_DataSet] $data
	 * @return Staple_Query_InsertMultiple
	 */
	public function setData(array $data)
	{
		//Check all the array values
		foreach ($data as $row)
		{
			if(!($row instanceof DataSet))
			{
				throw new Exception('To set the data for this object, the submission must be an array of Staple_Query_DataSet objects.', Error::APPLICATION_ERROR);
			}
		}
		$this->data = $data;
		return $this;
	}
}

?>