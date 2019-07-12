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
	 * @param bool|NULL $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function where($column, $operator, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->where($column, $operator, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $operator
	 * @param $value
	 * @param bool|NULL $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function orWhere($column, $operator, $value, bool $columnJoin = NULL, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->orWhere($column, $operator, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $operator
	 * @param $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereCondition($column, $operator, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true) : ModelSelectQuery
	{
		$this->queryObject->whereCondition($column, $operator, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $operator
	 * @param $value
	 * @param bool|NULL $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function orWhereCondition($column, $operator, $value, bool $columnJoin = NULL, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->orWhereCondition($column, $operator, $value, $columnJoin, $paramName, $parameterized);
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
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereEqual($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = null)
	{
		$this->queryObject->whereEqual($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function orWhereEqual($column, $value, bool $columnJoin = null,  string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->orWhereEqual($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereNotEqual($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = null)
	{
		$this->queryObject->whereNotEqual($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return $this
	 */
	public function orWhereNotEqual($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->orWhereNotEqual($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereLike($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->whereLike($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereNotLike($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->whereNotLike($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @return ModelSelectQuery
	 */
	public function whereNull($column)
	{
		$this->queryObject->whereNull($column);
		return $this;
	}

	/**
	 * @param $column
	 * @param $values
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereIn($column, $values, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->whereIn($column, $values, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $start
	 * @param $end
	 * @param string|null $startParamName
	 * @param string|null $endParamName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereBetween($column, $start, $end, string $startParamName = null, string $endParamName = null, bool $parameterized = true)
	{
		$this->queryObject->whereBetween($column, $start, $end, $startParamName, $endParamName, $parameterized);
		return $this;
	}

	/**
	 * @param mixed $limit
	 * @param int $offset
	 * @return ModelSelectQuery
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
	 * @return ModelSelectQuery
	 */
	public function columns(array $columns)
	{
		$this->queryObject->columns($columns);
		return $this;
	}

	/**
	 * @param string[] $columns
	 * @return ModelSelectQuery
	 */
	public function addColumnsArray(array $columns)
	{
		$this->queryObject->addColumnsArray($columns);
		return $this;
	}

	/**
	 * @param $order
	 * @return ModelSelectQuery
	 */
	public function orderBy($order)
	{
		$this->queryObject->orderBy($order);
		return $this;
	}

	/**
	 * @param $group
	 * @return ModelSelectQuery
	 */
	public function groupBy($group)
	{
		$this->queryObject->groupBy($group);
		return $this;
	}

	/**
	 * @return ModelSelectQuery
	 */
	public function clearHaving()
	{
		$this->queryObject->clearHaving();
		return $this;
	}

	/**
	 * @param $column
	 * @param $operator
	 * @param $value
	 * @param bool|NULL $columnJoin
	 * @param string|null $paramName
	 * @param bool|null $parameterized
	 * @return $this
	 */
	public function havingCondition($column, $operator, $value, bool $columnJoin = NULL, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->havingCondition($column, $operator, $value, $columnJoin);
		return $this;
	}

	public function havingStatement($statement)
	{
		$this->queryObject->havingStatement($statement);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool|null $parameterized
	 * @return $this
	 */
	public function havingEqual($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->havingEqual($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $column
	 * @param $value
	 * @param bool|null $columnJoin
	 * @param string|null $paramName
	 * @param bool|null $parameterized
	 * @return $this
	 */
	public function havingLike($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->havingLike($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	public function havingNull($column)
	{
		$this->queryObject->havingNull($column);
		return $this;
	}

	public function havingIn($column, array $values, string $paramName = null, bool $parameterized = true)
	{
		$this->queryObject->havingIn($column, $values, $paramName, $parameterized);
		return $this;
	}

	public function havingBetween($column, $start, $end, string $startParamName = null, string $endParamName = null, bool $parameterized = true)
	{
		$this->queryObject->havingBetween($column, $start, $end, $startParamName, $endParamName, $parameterized);
		return $this;
	}

	public function leftJoin($table, $condition, $alias = NULL, $schema = null)
	{
		$this->queryObject->leftJoin($table, $condition, $alias, $schema);
		return $this;
	}

	public function innerJoin($table, $condition, $alias = NULL, $schema = null)
	{
		$this->queryObject->innerJoin($table, $condition, $alias, $schema);
		return $this;
	}

	public function join($table, $condition, $alias = NULL, $schema = null)
	{
		$this->queryObject->join($table, $condition, $alias, $schema);
		return $this;
	}


}