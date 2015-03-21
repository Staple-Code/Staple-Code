<?php

/** 
 * A Class for creating a UNION query in MySQL
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

use \Staple\Exception\QueryException;
use \PDO;
use \Staple\Error;
use \Staple\Pager;
use \Exception;

class Union
{
	const DISTINCT = 'DISTINCT';
	const ALL = 'ALL';
	
	/**
	 * The database object. A database object is required to properly escape input.
	 * @var PDO
	 */
	protected $db;
	/**
	 * UNION flag: ALL | DISTINCT
	 * @var string
	 */
	protected $flag;
	/**
	 * An array of the queries to union.
	 * @var array[Staple_Query]
	 */
	protected $queries = array();
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
	 * Constructor accepts an array of Staple_Query_Select elements and a database connection.
	 * @param array $queries
	 * @param PDO $db
	 * @throws QueryException
	 */
	public function __construct(array $queries = array(), PDO $db = NULL)
	{
		//Process Database connection
		if($db instanceof PDO)
		{
			$this->setDb($db);
		}
		else
		{
			try {
				$this->setDb(Connection::get());
			}
			catch (Exception $e)
			{
				throw new QueryException('Unable to find a database connection.', Error::DB_ERROR, $e);
			}
		}
		if(!($this->db instanceof PDO))
		{
			throw new QueryException('Unable to create database object', Error::DB_ERROR);
		}
		
		foreach($queries as $q)
		{
			if($q instanceof Select)
			{
				$this->addQuery($q);
			}
		}
	}
	
	/**
	 * Builds the query on string conversion.
	 * @return string
	 */
	public function __toString()
	{
		return $this->build();
	}
	
	/**
	 * @return PDO $db
	 */
	public function getDb()
	{
		return $this->db;
	}
	
	/**
	 * Get the UNION flag: ALL | DISTINCT
	 */
	public function getFlag()
	{
		return $this->flag;
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
	 * @param PDO $db
	 * @return $this
	 */
	public function setDb(PDO $db)
	{
		$this->db = $db;
		return $this;
	}
	
	/**
	 * Set the UNION flag.
	 * @param string $flag
	 * @return $this
	 */
	public function setFlag($flag)
	{
		$flag = strtoupper($flag);
		switch($flag)
		{
			case self::ALL:
			case self::DISTINCT:
				$this->flag = $flag;
		}
		return $this;
	}
	
	/**
	 * Resets all of the columns in all the currently added queries to the specified column array.
	 * @param Query[] $columns
	 * @return $this
	 */
	public function setColumns(array $columns)
	{
		foreach($this->queries as $query)
		{
			$query->setColumns($columns);
		}
		return $this;
	}
	
	/**
	 * Set the order.
	 * @param string | array $order
	 * @return $this
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
		$this->limit = $limit;
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
	
	/**
	 * Add a query to the UNION
	 * @param Select $query
	 * @return $this
	 */
	public function addQuery(Select $query)
	{
		$this->queries[] = $query;
		return $this;
	}
	
	/**
	 * Remove a query from the UNION
	 * @param Select $query
	 * @return $this
	 */
	public function removeQuery(Select $query)
	{
		if(($key = array_search($query, $this->queries, true)) !== false)
		{
			unset($this->queries[$key]);
		}
		return $this;
	}

	/**
	 * Build the Query
	 * @return string
	 */
	function build()
	{
		$stmt = '';
		if(count($this->queries) <= 0)
		{
			return 'SELECT 0 FROM (SELECT 0) AS `a` WHERE 1=0';
		}
		elseif(count($this->queries) == 1)
		{
			$stmt .= 'SELECT * FROM ('.implode('', $this->queries).') AS `stapleunion` ';
		}
		else
		{
			if(isset($this->flag))
			{
				$glue = ")\nUNION {$this->flag} \n(";
			}
			else
			{
				$glue = ")\nUNION \n(";
			}
			$stmt .= '('.implode($glue, $this->queries).')';
		}
		
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
		
		return $stmt;
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
	 * Executes the query.
	 * @throws QueryException
	 * @return Statement | bool
	 */
	public function execute()
	{
		if($this->db instanceof PDO)
		{
			return $this->db->query($this->build());
		}
		else
		{
			try 
			{
				$this->db = Connection::get();
			}
			catch (Exception $e)
			{
				throw new QueryException('No Database Connection', Error::DB_ERROR);
			}
			if($this->db instanceof PDO)
			{
				return $this->db->query($this->build());
			}
		}
		return false;
	}
}

?>