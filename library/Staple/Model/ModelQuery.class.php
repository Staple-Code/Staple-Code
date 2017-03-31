<?php
/**
 * A base class to query models.
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


use Staple\Model;
use Staple\Query\IConnection;
use Staple\Query\IQuery;
use Staple\Query\IStatement;
use Staple\Query\Query;

class ModelQuery implements IQuery
{
	/**
	 * @var IQuery
	 */
	protected $queryObject;

	/**
	 * @var Model
	 */
	protected $model;

	/**
	 * ModelQuery constructor.
	 * @param Model $model
	 * @param IConnection $connection
	 */
	public function __construct(Model $model, IConnection $connection = NULL)
	{
		$this->setModel($model);
		$this->setQueryObject(Query::select());

		if(isset($connection))
			$this->queryObject->setConnection($connection);
	}

	public function __toString()
	{
		return $this->build();
	}

	/**
	 * @param Model $model
	 * @param IConnection $connection
	 * @return static
	 */
	public static function create(Model $model, IConnection $connection = NULL)
	{
		return new static($model, $connection);
	}

	/**
	 * @return IQuery
	 */
	public function getQueryObject(): IQuery
	{
		return $this->queryObject;
	}

	/**
	 * @param IQuery $queryObject
	 * @return ModelQuery
	 */
	public function setQueryObject(IQuery $queryObject): ModelQuery
	{
		$this->queryObject = $queryObject;
		return $this;
	}

	/**
	 * @return Model
	 */
	public function getModel(): Model
	{
		return $this->model;
	}

	/**
	 * @param Model $model
	 * @return ModelQuery
	 */
	public function setModel(Model $model): ModelQuery
	{
		$this->model = $model;
		return $this;
	}

	/**
	 * Alias of the model query first() method
	 * @return Model
	 */
	public function first() : Model
	{
		return $this->get()->first();
	}

	/**
	 * Alias of the model query first() method
	 * @return Model
	 */
	public function firstOrNull() : Model
	{
		return $this->get()->firstOrNull();
	}

	/**
	 * @return Model[]
	 */
	public function get() : ModelQueryResult
	{
		$models = [];

		//Set the table
		$this->queryObject->setTable($this->getModel()->_getTable());

		/** @var IStatement $result */
		$result = $this->getQueryObject()->execute();
		$query = $this->getQueryObject()->getConnection()->getLastQuery();
		if($result !== false)
		{
			while(($row = $result->fetch(\PDO::FETCH_ASSOC)) !== false)
			{
				$model = $this->getModel()->create();
				$model->_setData($row);
				$models[] = $model;
			}
		}

		return ModelQueryResult::create($models, $this->getConnection(), $query);
	}

	/**
	 * @return string
	 */
	public function getTable() : string
	{
		return $this->queryObject->getTable();
	}

	/**
	 * @return IConnection
	 */
	public function getConnection() : IConnection
	{
		return $this->queryObject->getConnection();
	}

	/**
	 * @param mixed $table
	 * @param string $alias
	 * @return IQuery
	 */
	public function setTable($table, $alias = NULL) : IQuery
	{
		$this->queryObject->setTable($table, $alias);
		return $this;
	}

	/**
	 * @param IConnection $connection
	 * @return IQuery
	 */
	public function setConnection(IConnection $connection) : IQuery
	{
		$this->queryObject->setConnection($connection);
		return $this;
	}

	/**
	 * @return string
	 */
	public function build() : string
	{
		return $this->queryObject->build();
	}

	/**
	 * Alias of get()
	 * @param IConnection|NULL $connection
	 * @return mixed
	 */
	public function execute(IConnection $connection = NULL)
	{
		return $this->get();
	}
}