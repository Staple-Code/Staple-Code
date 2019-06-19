<?php
/**
 * A class to perform queries on model context.
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


use Staple\Query\ISelectQuery;

class ModelSelectQuery extends ModelQuery implements ISelectQuery
{
	/** @var ISelectQuery $queryObject */
	protected $queryObject;

	/**
	 * @return ModelSelectQuery
	 */
	public function clearWhere()
	{
		$this->queryObject->clearWhere();
		return $this;
	}

	/**
	 * @param $column
	 * @param $operator
	 * @param $value
	 * @param null $columnJoin
	 * @return ModelSelectQuery
	 */
	public function whereCondition($column, $operator, $value, $columnJoin = NULL) : ModelSelectQuery
	{
		$this->queryObject->whereCondition($column, $operator, $value, $columnJoin);
		return $this;
	}

	/**
	 * @param $statement
	 * @return ModelSelectQuery
	 */
	public function whereStatement($statement)
	{
		$this->queryObject->whereStatement($statement);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param string $paramName
	 * @param null $columnJoin
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereEqual(string $column, $value, string $paramName = null, $columnJoin = NULL, bool $parameterized = null)
	{
		$this->queryObject->whereEqual($column, $value, $columnJoin);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param null $columnJoin
	 * @return ModelSelectQuery
	 */
	public function whereNotEqual($column, $value, $columnJoin = NULL)
	{
		$this->queryObject->whereNotEqual($column, $value, $columnJoin);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @return ModelSelectQuery
	 */
	public function whereLike($column, $value)
	{
		$this->queryObject->whereLike($column, $value);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @return $this
	 */
	public function whereNotLike($column, $value)
	{
		$this->queryObject->whereNotLike($column, $value);
		return $this;
	}

	/**
	 * @param $column
	 * @return $this
	 */
	public function whereNull($column)
	{
		$this->queryObject->whereNull($column);
		return $this;
	}

	/**
	 * @param $column
	 * @param $values
	 * @return $this
	 */
	public function whereIn($column, $values)
	{
		$this->queryObject->whereIn($column, $values);
		return $this;
	}

	/**
	 * @param $column
	 * @param $start
	 * @param $end
	 * @return $this
	 */
	public function whereBetween($column, $start, $end)
	{
		$this->queryObject->whereBetween($column, $start, $end);
		return $this;
	}

	/**
	 * @param mixed $limit
	 * @param int $offset
	 * @return $this
	 */
	public function limit($limit, $offset = NULL)
	{
		$this->queryObject->limit($limit, $offset);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getColumns()
	{
		return $this->queryObject->getColumns();
	}

	public function addColumn($col, $name = NULL)
	{
		$this->queryObject->addColumn($col, $name);
		return $this;
	}

	/**
	 * @param array $columns
	 * @return $this
	 */
	public function columns(array $columns)
	{
		$this->queryObject->columns($columns);
		return $this;
	}

	public function addColumnsArray(array $columns)
	{
		$this->queryObject->addColumnsArray($columns);
		return $this;
	}

	/**
	 * @param $order
	 * @return $this
	 */
	public function orderBy($order)
	{
		$this->queryObject->orderBy($order);
		return $this;
	}

	/**
	 * @param $group
	 * @return $this
	 */
	public function groupBy($group)
	{
		$this->queryObject->groupBy($group);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearHaving()
	{
		$this->queryObject->clearHaving();
		return $this;
	}

	public function havingCondition($column, $operator, $value, $columnJoin = NULL)
	{
		$this->queryObject->havingCondition($column, $operator, $value, $columnJoin);
		return $this;
	}

	public function havingStatement($statement)
	{
		$this->queryObject->havingStatement($statement);
		return $this;
	}

	public function havingEqual($column, $value, $columnJoin = NULL)
	{
		$this->queryObject->havingEqual($column, $value, $columnJoin);
		return $this;
	}

	public function havingLike($column, $value)
	{
		$this->queryObject->havingLike($column, $value);
		return $this;
	}

	public function havingNull($column)
	{
		$this->queryObject->havingNull($column);
		return $this;
	}

	public function havingIn($column, array $values)
	{
		$this->queryObject->havingIn($column, $values);
		return $this;
	}

	public function havingBetween($column, $start, $end)
	{
		$this->queryObject->havingBetween($column, $start, $end);
		return $this;
	}

	public function leftJoin($table, $condition, $alias = NULL)
	{
		$this->queryObject->leftJoin($table, $condition, $alias);
		return $this;
	}

	public function innerJoin($table, $condition, $alias = NULL)
	{
		$this->queryObject->innerJoin($table, $condition, $alias);
		return $this;
	}

	public function join($table, $condition, $alias = NULL)
	{
		$this->queryObject->join($table, $condition, $alias);
		return $this;
	}


}