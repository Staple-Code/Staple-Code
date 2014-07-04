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
	
	
	public function __construct()
	{
		
	}
	
	public function build()
	{
		return 'blah';
	}
	
	public function addRow(array $row)
	{
		if($this->rows instanceof Staple_Query_Select)
		{
			throw new Exception('Cannot add a row when using a sub-query.', Staple_Error::DB_ERROR);
		}
		else 
		{
			$this->rows[] = $row;
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
}

?>