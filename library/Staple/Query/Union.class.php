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
use \Staple\Error;
use \Staple\Pager;
use \Exception;

class Union
{
	const DISTINCT = 'DISTINCT';
	const ALL = 'ALL';
	
	/**
	 * The database object. A database object is required to properly escape input.
	 * @var Connection
	 */
	protected $connection;
	/**
	 * UNION flag: ALL | DISTINCT
	 * @var string
	 */
	protected $flag;
	/**
	 * Select statement flags
	 * @var array
	 */
	protected $selectFlags = [];
	/**
	 * An array of the queries to union.
	 * @var Select[]
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
	 * Constructor accepts an array of Select elements and a database connection.
	 * @param array $queries
	 * @param Connection $connection
	 * @throws QueryException
	 */
	public function __construct(array $queries = array(), Connection $connection = NULL)
	{
		//Process Database connection
		if($connection instanceof Connection)
		{
			$this->setDb($connection);
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
		if(!($this->connection instanceof Connection))
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
		try
		{
			return $this->build();
		}
		catch(QueryException $e)
		{
			return 'A query exception occurred when building the query string.';
		}
	}
	
	/**
	 * @return Connection $db
	 */
	public function getDb()
	{
		return $this->connection;
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
	 * @param Connection $db
	 * @return $this
	 */
	public function setDb(Connection $db)
	{
		$this->connection = $db;
		return $this;
	}

	/**
	 * Retrieve the current connection.
	 * @return Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Set the connection on the Union object.
	 * @param Connection $connection
	 * @return $this
	 */
	public function setConnection($connection)
	{
		$this->connection = $connection;
		return $this;
	}

	/**
	 * Add a select flag on the query.
	 * @param $flag
	 * @return $this
	 */
	public function addSelectFlag($flag)
	{
		switch($flag)
		{
			case Select::ALL:
			case Select::DISTINCT:
			case Select::DISTINCTROW:
			case Select::HIGH_PRIORITY:
			case Select::STRAIGHT_JOIN:
			case Select::SQL_SMALL_RESULT:
			case Select::SQL_BIG_RESULT:
			case Select::SQL_BUFFER_RESULT:
			case Select::SQL_CACHE:
			case Select::SQL_NO_CACHE:
			case Select::SQL_CALC_FOUND_ROWS:
				$this->selectFlags[] = $flag;
				break;
		}
		return $this;
	}

	/**
	 * Clear any select flags added to the query.
	 * @return $this
	 */
	public function clearSelectFlags()
	{
		$this->selectFlags = array();
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
	 * @param array $columns
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
	 * Return the array of select query objects.
	 * @return Select[]
	 */
	public function getQueries()
	{
		return $this->queries;
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
	 * @throws QueryException
	 */
	function build()
	{
		$stmt = 'SELECT ';

		//SELECT Statement Flags
		if(count($this->selectFlags) > 0)
		{
			$stmt .= ' '.implode(' ', $this->selectFlags);
		}

		//SQL Server Limit - when offset is zero
		if($this->getLimit() > 0
			&& $this->getLimitOffset() == 0
			&& $this->getConnection()->getDriver() == Connection::DRIVER_SQLSRV)
		{
			$stmt .= ' TOP ' . $this->getLimit().' ';
		}

		//Throw exception if no queries exist.
		if(count($this->queries) <= 0)
		{
			throw new QueryException('No queries were supplied to union.');
		}
		elseif(count($this->queries) == 1)
		{
			//Render the union statement into a sub-select statement when only one query is attached.
			$stmt .= "\n\t*\n\tFROM (".implode('', $this->queries).')';

			//Switch the method based on database driver of the current connection
			switch($this->getConnection()->getDriver())
			{
				case Connection::DRIVER_MYSQL:
					$stmt .= " AS `stapleunion`";
					break;
				default:
					$stmt .= " AS stapleunion";
			}
		}
		else
		{
			//SQL Server Limit - when offset is zero
			if($this->getLimit() > 0
				&& $this->getLimitOffset() == 0
				&& $this->getConnection()->getDriver() == Connection::DRIVER_SQLSRV)
			{
				$stmt .= 'TOP ' . $this->getLimit();
			}

			//Start the union as a sub-query.
			$stmt .=  "\n\t*\n\tFROM (";

			//Union the statements together with optional flags
			if(isset($this->flag))
			{
				$glue = "\nUNION {$this->flag} \n";
			}
			else
			{
				$glue = "\nUNION \n";
			}
			$stmt .= implode($glue, $this->queries);
			$stmt .= ')';

			//Switch the method based on database driver of the current connection
			switch($this->getConnection()->getDriver())
			{
				case Connection::DRIVER_MYSQL:
					$stmt .= " AS `stapleunion`";
					break;
				default:
					$stmt .= " AS stapleunion";
			}
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

			//SQL Server 2012 Pagination
			if($this->getConnection()->getDriver() == Connection::DRIVER_SQLSRV)
			{
				if (isset($this->limit) && !isset($sql2005limit) && $this->getLimitOffset() != 0)
				{
					//Offset
					$stmt .= "\n\tOFFSET " . $this->getLimitOffset(). ' ROWS ';

					//Limit
					$stmt .= "\n\tFETCH NEXT " . $this->getLimit(). ' ROWS ';
				}
			}
		}
		
		//LIMIT CLAUSE
		if($this->getConnection()->getDriver() == Connection::DRIVER_MYSQL)
		{
			if (isset($this->limit))
			{
				$stmt .= "\n\tLIMIT " . $this->getLimit();
				if (isset($this->limitOffset))
				{
					$stmt .= ' OFFSET ' . $this->limitOffset;
				}
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
		if($this->connection instanceof Connection)
		{
			return $this->connection->query($this->build());
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
			if($this->connection instanceof Connection)
			{
				return $this->connection->query($this->build());
			}
		}
		return false;
	}
}