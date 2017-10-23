<?php
 
/** 
 * A class for creating SQL DELETE statements
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

class Delete extends Query
{
	const IGNORE = 'IGNORE';
	const LOW_PRIORITY = 'LOW_PRIORITY';
	const QUICK = 'QUICK';
	
	/**
	 * Additional Query Flags
	 * @var array[string]
	 */
	protected $flags = array();
	/**
	 * Array of Staple_Query_Join objects that represent table joins on the query
	 * @var Join[]
	 */
	protected $joins = array();

	/**
	 * @param string $table
	 * @param Connection $db
	 * @throws \Exception
	 */
	public function __construct($table = NULL, Connection $db = NULL)
	{
		parent::__construct($table, $db);
	}
	
	public function addFlag($flag)
	{
		switch($flag)
		{
			case self::IGNORE:
			case self::LOW_PRIORITY:
			case self::QUICK:
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

	/*-----------------------------------------------JOIN FUNCTIONS-----------------------------------------------*/
	
	public function addJoin(Join $join)
	{
		$this->joins[] = $join;
	}
	
	public function removeJoin($table)
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
	
	public function leftJoin($table, $condition)
	{
		$this->addJoin(Join::left($table, $condition));
		return $this;
	}
	
	public function innerJoin($table, $condition)
	{
		$this->addJoin(Join::inner($table, $condition));
		return $this;
	}
	
	public function getJoins()
	{
		return $this->joins;
	}
	
	/*-----------------------------------------------BUILD FUNCTION-----------------------------------------------*/
	
	/**
	 * 
	 * @see Staple_Query::build()
	 */
	function build()
	{
		$stmt = 'DELETE ';
		
		//Flags
		if(count($this->flags) > 0)
		{
			$stmt .= ' '.implode(' ', $this->flags);
		}
		
		//FROM CLAUSE
		$stmt .= "FROM ";

		//Table
		if(isset($this->schema))
		{
			$stmt .= $this->schema.'.';
		}
		elseif(!empty($this->connection->getSchema()))
		{
			$stmt .= $this->connection->getSchema().'.';
		}
		$stmt .= $this->table;
		
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
		
		return $stmt;
	}
}