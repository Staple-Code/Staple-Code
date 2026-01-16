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


use Staple\Pager;
use Staple\Query\ISelectQuery;

class ModelSelectQuery extends ModelQuery implements ISelectQuery
{
	/** @var ISelectQuery $queryObject */
	protected \Staple\Query\IQuery $queryObject;

	/**
	 * @return ModelSelectQuery
	 */
	public function clearWhere(): static
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
	public function where($column, $operator, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true): static
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
	public function orWhere($column, $operator, $value, bool $columnJoin = NULL, string $paramName = null, bool $parameterized = true): static
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
	public function orWhereCondition($column, $operator, $value, bool $columnJoin = NULL, string $paramName = null, bool $parameterized = true): static
	{
		$this->queryObject->orWhereCondition($column, $operator, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param $statement
	 * @return ModelSelectQuery
	 */
	public function whereStatement($statement): static
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
	public function whereEqual($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true): static
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
	public function orWhereEqual($column, $value, bool $columnJoin = null,  string $paramName = null, bool $parameterized = true): static
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
	public function whereNotEqual($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = null): static
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
	public function orWhereNotEqual($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true): static
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
	public function whereLike($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true): static
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
	public function whereNotLike($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true): static
	{
		$this->queryObject->whereNotLike($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	/**
	 * @param string $column
	 * @return ModelSelectQuery
	 */
	public function whereNull(string $column): static
	{
		$this->queryObject->whereNull($column);
		return $this;
	}

	/**
	 * @param string $column
	 * @param mixed $values
	 * @param string|null $paramName
	 * @param bool $parameterized
	 * @return ModelSelectQuery
	 */
	public function whereIn(string $column, mixed $values, string $paramName = null, bool $parameterized = true): static
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
	public function whereBetween($column, $start, $end, string $startParamName = null, string $endParamName = null, bool $parameterized = true): static
	{
		$this->queryObject->whereBetween($column, $start, $end, $startParamName, $endParamName, $parameterized);
		return $this;
	}

	/**
	 * @param int|Pager $limit
	 * @param int|null $offset
	 * @return ModelSelectQuery
	 */
	public function limit(int|Pager $limit, int $offset = NULL): static
	{
		$this->queryObject->limit($limit, $offset);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getColumns(): mixed
	{
		return $this->queryObject->getColumns();
	}

	public function addColumn(string $col, string $name = NULL): static
	{
		$this->queryObject->addColumn($col, $name);
		return $this;
	}

	/**
	 * @param array $columns
	 * @return ModelSelectQuery
	 */
	public function columns(array $columns): static
	{
		$this->queryObject->columns($columns);
		return $this;
	}

	/**
	 * @param string[] $columns
	 * @return ModelSelectQuery
	 */
	public function addColumnsArray(array $columns): static
	{
		$this->queryObject->addColumnsArray($columns);
		return $this;
	}

	/**
	 * @param $order
	 * @return ModelSelectQuery
	 */
	public function orderBy($order): static
	{
		$this->queryObject->orderBy($order);
		return $this;
	}

	/**
	 * @param $group
	 * @return ModelSelectQuery
	 */
	public function groupBy($group): static
	{
		$this->queryObject->groupBy($group);
		return $this;
	}

	/**
	 * @return ModelSelectQuery
	 */
	public function clearHaving(): static
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
	public function havingCondition($column, $operator, $value, bool $columnJoin = NULL, string $paramName = null, bool $parameterized = true): static
	{
		$this->queryObject->havingCondition($column, $operator, $value, $columnJoin);
		return $this;
	}

	public function havingStatement($statement): static
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
	public function havingEqual($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true): static
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
	public function havingLike($column, $value, bool $columnJoin = null, string $paramName = null, bool $parameterized = true): static
	{
		$this->queryObject->havingLike($column, $value, $columnJoin, $paramName, $parameterized);
		return $this;
	}

	public function havingNull($column): static
	{
		$this->queryObject->havingNull($column);
		return $this;
	}

	public function havingIn($column, array $values, string $paramName = null, bool $parameterized = true): static
	{
		$this->queryObject->havingIn($column, $values, $paramName, $parameterized);
		return $this;
	}

	public function havingBetween($column, $start, $end, string $startParamName = null, string $endParamName = null, bool $parameterized = true): static
	{
		$this->queryObject->havingBetween($column, $start, $end, $startParamName, $endParamName, $parameterized);
		return $this;
	}

	public function leftJoin($table, $condition, $alias = NULL, $schema = null): static
	{
		$this->queryObject->leftJoin($table, $condition, $alias, $schema);
		return $this;
	}

	public function innerJoin($table, $condition, $alias = NULL, $schema = null): static
	{
		$this->queryObject->innerJoin($table, $condition, $alias, $schema);
		return $this;
	}

	public function join($table, $condition, $alias = NULL, $schema = null): static
	{
		$this->queryObject->join($table, $condition, $alias, $schema);
		return $this;
	}


}