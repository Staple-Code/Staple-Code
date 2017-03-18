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

class ModelQueryResult implements \Iterator, \ArrayAccess
{
	/**
	 * @var int
	 */
	private $position = 0;

	/**
	 * @var Model[]
	 */
	protected $results = [];

	public function __construct(array $results = null)
	{
		if(isset($results))
			$this->setResults($results);
	}

	public static function create(array $results = null)
	{
		return new static($results);
	}

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

	public function current()
	{
		return $this->results[$this->position];
	}

	public function next()
	{
		++$this->position;
	}

	public function key()
	{
		return $this->position;
	}

	public function valid()
	{
		return isset($this->results[$this->position]);
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function offsetExists($offset) : bool
	{
		return isset($this->results[$offset]);
	}

	public function offsetGet($offset) : Model
	{
		return $this->results[$offset];
	}

	public function offsetSet($offset, $value)
	{
		if(is_null($offset))
			$this->results[] = $value;
		else
			$this->results[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->results[$offset]);
	}
}