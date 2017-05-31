<?php
/**
 * A class for returning results from a model query.
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

namespace Staple\Model;


use Staple\Exception\ModelNotFoundException;
use Staple\Model;
use Staple\Query\Connection;
use Staple\Query\IConnection;

class ModelQueryResult implements \Iterator, \ArrayAccess, \JsonSerializable
{
	/**
	 * @var int
	 */
	private $position = 0;

	/**
	 * @var Model[]
	 */
	protected $results = [];

	/**
	 * @var IConnection
	 */
	protected $connection;

	/**
	 * @var string
	 */
	protected $query;

	/**
	 * ModelQueryResult constructor.
	 * @param array|null $results
	 * @param IConnection|NULL $connection
	 * @param string|NULL $query
	 */
	public function __construct(array $results = NULL, IConnection $connection = NULL, string $query = NULL)
	{
		if(isset($results))
			$this->setResults($results);

		if(isset($connection))
			$this->setConnection($connection);		//Supplied Connection
		else
			$this->setConnection(Connection::get());	//Default connection

		if(isset($query))
			$this->setQuery($query);
	}

	/**
	 * Factory method to create a ModelQueryResult object
	 * @param array|NULL $results
	 * @param IConnection|NULL $connection
	 * @param string|NULL $query
	 * @return static
	 */
	public static function create(array $results = NULL, IConnection $connection = NULL, string $query = NULL)
	{
		return new static($results, $connection, $query);
	}

	/**
	 * Return the query results to be json encoded.
	 * @return array
	 */
	function jsonSerialize()
	{
		return $this->results;
	}


	/**
	 * Return the count of the results of the query.
	 * @return int
	 */
	public function count()
	{
		return count($this->results);
	}

	/**
	 * Returns the first retrieved Model object, throws exception on failure
	 * @return Model
	 * @throws ModelNotFoundException
	 */
	public function first() : Model
	{
		if(count($this->results) >= 1)
		{
			reset($this->results);
			return current($this->results);
		}
		else
			throw new ModelNotFoundException();

	}

	/**
	 * Returns the first retrieved Model object, returns null on failure
	 * @return Model|null
	 */
	public function firstOrNull() : Model
	{
		if(count($this->results) >= 1)
		{
			reset($this->results);
			return current($this->results);
		}
		else
			return NULL;
	}

	/**
	 * Alias of getResults() method.
	 * @return Model[]
	 */
	public function toArray()
	{
		return $this->getResults();
	}

	/**
	 * Alias of getResults() method.
	 * @return array|Model[]
	 */
	public function all()
	{
		return $this->getResults();
	}

	/**
	 * @return Model[]
	 */
	public function getResults(): array
	{
		return $this->results;
	}

	/**
	 * @param Model[] $results
	 * @return ModelQueryResult
	 */
	public function setResults(array $results): ModelQueryResult
	{
		$this->results = $results;
		return $this;
	}

	/**
	 * Get the connection object.
	 * @return IConnection
	 */
	public function getConnection(): IConnection
	{
		return $this->connection;
	}

	/**
	 * Set the connection object
	 * @param IConnection $connection
	 * @return ModelQueryResult
	 */
	public function setConnection(IConnection $connection): ModelQueryResult
	{
		$this->connection = $connection;
		return $this;
	}

	/**
	 * Get the Query String
	 * @return string
	 */
	public function getQuery(): string
	{
		return $this->query;
	}

	/**
	 * Set the query string
	 * @param string $query
	 * @return ModelQueryResult
	 */
	protected function setQuery(string $query): ModelQueryResult
	{
		$this->query = $query;
		return $this;
	}

	/*--------------------------------------ITERATION METHODS--------------------------------------*/

	/**
	 * @return Model
	 */
	public function current()
	{
		return $this->results[$this->position];
	}

	public function next()
	{
		++$this->position;
	}

	/**
	 * @return int
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * @return bool
	 */
	public function valid() : bool
	{
		return isset($this->results[$this->position]);
	}

	public function rewind()
	{
		$this->position = 0;
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset) : bool
	{
		return isset($this->results[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return Model
	 */
	public function offsetGet($offset) : Model
	{
		return $this->results[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		if(is_null($offset))
			$this->results[] = $value;
		else
			$this->results[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->results[$offset]);
	}
}