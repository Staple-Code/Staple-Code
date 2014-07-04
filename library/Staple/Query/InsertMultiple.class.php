<?php

/** 
 * @author Ironpilot
 * 
 * 
 */
class Staple_Query_InsertMultiple extends Staple_Query_Insert
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
				$this->setDb(Staple_DB::get());
			}
			catch (Exception $e)
			{
				$this->setDb(new mysqli());
			}
		}
		//No DB = Bad
		if(!($this->db instanceof mysqli))
		{
			throw new Exception('Unable to create database object', Staple_Error::DB_ERROR);
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
			if($dataset instanceof Staple_Query_DataSet)
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
	public function addRow(Staple_Query_DataSet $row)
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
			if(!($row instanceof Staple_Query_DataSet))
			{
				throw new Exception('To set the data for this object, the submission must be an array of Staple_Query_DataSet objects.', Staple_Error::APPLICATION_ERROR);
			}
		}
		$this->data = $data;
		return $this;
	}
}

?>