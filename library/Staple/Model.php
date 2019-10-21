<?php

/**
 * A parent class for models in STAPLE.
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
namespace Staple;

use Exception;
use PDO;
use ReflectionClass;
use ReflectionProperty;
use Staple\Exception\ModelNotFoundException;
use Staple\Exception\QueryException;
use Staple\Model\ModelQuery;
use Staple\Model\ModelSelectQuery;
use Staple\Query\Connection;
use Staple\Query\IConnection;
use Staple\Query\Insert;
use Staple\Query\IStatement;
use Staple\Query\Query;
use Staple\Query\Select;
use Staple\Traits\Factory;
use stdClass;

abstract class Model implements \JsonSerializable, \ArrayAccess
{
	use Factory;
	/**
	 * Primary Key Column Name. Use a string for a single primary key column, an array for a composite key.
	 * @var string | array
	 */
	protected $_primaryKey = 'id';
	/**
	 * The table name of the model if different from the object name.
	 * @var string
	 */
	protected $_table;
	/**
	 * Dynamic Properties of the model.
	 * @var array
	 */
	protected $_data = array();
	/**
	 * A database connection object that the model uses
	 * @var IConnection
	 */
	protected $_connection;
	/**
	 * Bool to decide between soft deletes and hard deletes.
	 * @var bool
	 */
	protected $_softDelete = false;
	/**
	 * The column name of the soft delete column.
	 * @var string
	 */
	protected $_softDeleteField = 'deleted_at';

	/**
	 *
	 * @param array $options
	 */
	public function __construct(array $options = NULL)
	{
		//Setup the table name if not already set.
		if(!isset($this->_table))
			$this->_setupTableName();

		//Check for the options variable.
		if(is_array($options))
			$this->_options($options);
	}

	/**
	 *
	 * Allows dynamic setting of Model properties
	 * @param string $name
	 * @param string|int|float $value
	 * @throws Exception
	 */
	public function __set($name, $value)
	{
		$method = 'set' . ucfirst($name);
		if(method_exists($this, $method))
		{
			//Use the setter built onto the object
			$this->$method($value);
		}
		else
		{
			//Set the property dynamically
			$this->_data[$name] = $value;
		}
	}

	/**
	 *
	 * Allows dynamic calling of Model properties
	 * @param string $name
	 * @throws Exception
	 * @return mixed
	 */
	public function __get($name)
	{
		$method = 'get' . ucfirst($name);
		if(method_exists($this, $method))
		{
			return $this->$method();
		}
		elseif(isset($this->_data[$name]))
		{
			return $this->_data[$name];
		}
		else
		{
			throw new Exception('Property does not exist on this model.');
		}
	}

	/**
	 * Return the set status of the dynamic model properties
	 * @param $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->_data[$name]);
	}

	/**
	 * Unset a dynamic property of the model
	 * @param $name
	 */
	public function __unset($name)
	{
		if(isset($this->_data[$name]))
			unset($this->_data[$name]);
	}

	/**
	 * Dynamically call properties without having to create getters and setters.
	 * @param string $name
	 * @param array $arguments
	 * @throws Exception
	 * @return mixed
	 */
	public function __call($name, array $arguments)
	{
		if(strtolower(substr($name, 0, 3)) == 'get')
		{
			$dataName = Utility::snakeCase(substr($name, 3));
			if(isset($this->_data[$dataName]))
			{
				return $this->_data[$dataName];
			}
		}
		elseif(strtolower(substr($name, 0, 3)) == 'set')
		{
			$dataName = Utility::snakeCase(substr($name, 3));
			$this->_data[$dataName] = array_shift($arguments);
			return $this;
		}

		throw new Exception(' Call to undefined method ' . $name);
	}

	/**
	 * Convert the model to JSON when performing a string conversion
	 * @return string
	 */
	public function __toString()
	{
		return json_encode($this);
	}

	/**
	 *
	 * Sets model properties supplied via an associative array.
	 * @param array $options
	 * @return $this
	 */
	public function _options($options)
	{
		foreach($options as $key => $value)
		{
			$method = 'set' . ucfirst($key);
			$method2 = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
			if(method_exists($this, $method))
			{
				$this->$method($value);
			}
			elseif(method_exists($this, $method2))
			{
				$this->$method2($value);
			}
		}
		return $this;
	}

	/**
	 * Sets the table name based on the name of the model class
	 */
	protected function _setupTableName()
	{
		//Get the class name of this object
		$class = get_class($this);

		//Explode out the namespace tree
		$namespaceTree = explode('\\', $class);

		//Snake_case the object name
		$name = Utility::snakeCase(array_pop($namespaceTree));

		//Split and find the final word in the class name
		$words = explode('_', $name);
		$finalWord = array_pop($words);

		//Check that the final word is not "model"
		if($finalWord == 'model')
			$finalWord = array_pop($words);

		//pluralize the final word
		$plural = Utility::pluralize($finalWord);

		//Push it back into the array
		array_push($words, $plural);

		//Collapse and set the table name
		$this->_table = implode('_', $words);
	}

	/**
	 * Get the current table name that this model is attached to.
	 * @return string
	 */
	public function _getTable()
	{
		return $this->_table;
	}

	/**
	 * Manually set the model data.
	 * @param $data
	 * @return $this
	 */
	public function _setData($data)
	{
		$this->_data = $data;
		return $this;
	}

	/**
	 *
	 */
	public function jsonSerialize()
	{
		$exclude = ['_primaryKey', '_table', '_data', '_connection','_softDelete','_softDeleteField'];
		$reflect = new ReflectionClass($this);
		$props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

		$object = new stdClass();
		foreach($this->_data as $key => $data)
		{
			$object->$key = $data;
		}

		foreach($props as $prop)
		{
			$name = $prop->getName();
			if(in_array($name, $exclude) === false)
			{
				$object->$name = $this->$name;
			}
		}

		return $object;
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{
		$method = 'get' . ucfirst($offset);
		if(method_exists($this, $method))
		{
			return $this->$method();
		}
		elseif(isset($this->_data[$offset]))
		{
			return $this->_data[$offset];
		}
		else
		{
			return NULL;
		}
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		$method = 'set' . ucfirst($offset);
		if(method_exists($this, $method))
		{
			//Use the setter built onto the object
			$this->$method($value);
		}
		else
		{
			//Set the property dynamically
			$this->_data[$offset] = $value;
		}
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset)
	{
		if(isset($this->_data[$offset]))
			unset($this->_data[$offset]);
	}

	/**
	 * @return IConnection $_connection
	 */
	public function getConnection()
	{
		if(isset($this->_connection))        //Return the specified model connection
		{
			return $this->_connection;
		}
		else                            //Return the default connection
		{
			return Connection::get();
		}
	}

	/**
	 * @param IConnection $connection
	 * @return $this
	 */
	public function setConnection(IConnection $connection)
	{
		$this->_connection = $connection;
		return $this;
	}

	/**
	 * Save the model to the database
	 * @return boolean
	 */
	public function save()
	{
		//if the primary key has been set use update, otherwise insert.
		if(isset($this->_data[$this->_primaryKey]))
		{
			$data = $this->_data;
			unset($data[$this->_primaryKey]);
			$query = Query::update($this->_getTable(), $data, $this->getConnection())
				->whereEqual($this->_primaryKey, $this->_data[$this->_primaryKey]);
		}
		else
		{
			$query = Query::insert($this->_getTable(), $this->_data, $this->getConnection());
		}

		//Execute the query and return the result
		$result = $query->execute();

		//check for a new ID and apply it to the data set.
		/** @var Insert $query */
		if($query instanceof Insert && $result != false)
			$this->_data[$this->_primaryKey] = $query->getInsertId();

		//Return the boolean of success or failure.
		return $result;
	}

	/**
	 * Return an instance of the model from the primary key.
	 * @param int $id
	 * @param IConnection $connection
	 * @return $this | $this[]
	 * @throws ModelNotFoundException
	 */
	public static function find($id, IConnection $connection = NULL)
	{
		//Make a model instance
		$model = static::make();

		//Create the query
		$query = Select::table($model->_getTable())->whereEqual($model->_primaryKey, $id);

		//Change connection if needed
		if(isset($connection)) $query->setConnection($connection);

		//Execute the query
		$result = $query->execute();
		if($result instanceof IStatement)
		{
			$models = array();
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{
				$model = static::make();
				$model->_data = $row;
				$models[] = $model;
			}

			if(count($models) == 1)
				return array_pop($models);
			elseif(count($models) > 1)
				return $models;
			else
				throw new ModelNotFoundException();
		}

		throw new ModelNotFoundException();
	}

	/**
	 * Returns all of the models in an array.
	 * @param mixed $order
	 * @param mixed $limit
	 * @param IConnection|NULL $connection
	 * @return $this[]
	 * @throws QueryException
	 * @throws ModelNotFoundException
	 */
	public static function findAll($order = NULL, $limit = NULL, IConnection $connection = NULL)
	{
		//Make a model instance
		$model = static::make();

		//Create the query
		$query = Select::table($model->_getTable());

		//Change connection if needed
		if(isset($connection)) $query->setConnection($connection);

		//Set order
		if(isset($order)) $query->orderBy($order);

		//Set limit
		if(isset($limit)) $query->limit($limit);

		//Execute the query
		$result = $query->execute();
		if($result instanceof IStatement)
		{
			$models = [];
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{
				$model = static::make();
				$model->_data = $row;
				$models[] = $model;
			}
			if(count($models) >= 1)
				return $models;
		}

		throw new ModelNotFoundException();
	}

	/**
	 * @param string $column
	 * @param mixed $value
	 * @param int $limit
	 * @param IConnection $connection
	 * @return $this[]
	 * @throws QueryException
	 * @throws ModelNotFoundException
	 */
	public static function findWhereEqual($column, $value, $order = NULL, $limit = NULL, IConnection $connection = NULL)
	{
		//Make a model instance
		$model = static::make();

		//Create the query
		$query = Select::table($model->_getTable())->whereEqual($column, $value);

		//Change connection if needed
		if(isset($connection)) $query->setConnection($connection);

		//Set order
		if(isset($order)) $query->orderBy($order);

		//Set limit
		if(isset($limit)) $query->limit($limit);

		//Execute the query
		$result = $query->execute();
		if($result instanceof IStatement)
		{
			//If more than one record was returned return the array of results.
			$models = array();
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{
				$model = static::make();
				$model->_data = $row;
				$models[] = $model;
			}
			if(count($models) >= 1)
				return $models;
		}

		throw new ModelNotFoundException();
	}

	/**
	 * Find models where specified column is null.
	 * @param string $column
	 * @param int|Pager $limit
	 * @param IConnection|NULL $connection
	 * @return array
	 * @throws ModelNotFoundException
	 */
	public static function findWhereNull($column, $order = NULL, $limit = NULL, IConnection $connection = NULL)
	{
		//Make a model instance
		$model = static::make();

		//Create the query
		$query = Query::select($model->_getTable())->whereNull($column);

		//Change connection if needed
		if(isset($connection)) $query->setConnection($connection);

		//Set order
		if(isset($order)) $query->orderBy($order);

		//Set limit
		if(isset($limit)) $query->limit($limit);

		//Execute the query
		$result = $query->execute();
		if($result instanceof IStatement)
		{
			//If more than one record was returned return the array of results.
			$models = array();
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{
				$model = static::make();
				$model->_data = $row;
				$models[] = $model;
			}
			if(count($models) >= 1)
				return $models;
		}
		
		throw new ModelNotFoundException();
	}

	/**
	 * Find models using a WHERE column IN() clause
	 * @param string $column
	 * @param array $values
	 * @param int|Pager $limit
	 * @param IConnection|NULL $connection
	 * @return array
	 * @throws ModelNotFoundException
	 */
	public static function findWhereIn($column, array $values, $order = NULL, $limit = NULL, IConnection $connection = NULL)
	{
		//Make a model instance
		$model = static::make();

		//Create the query
		$query = Query::select($model->_getTable())->whereIn($column, $values);

		//Change connection if needed
		if(isset($connection)) $query->setConnection($connection);

		//Set order
		if(isset($order)) $query->orderBy($order);

		//Set limit
		if(isset($limit)) $query->limit($limit);

		//Execute the query
		$result = $query->execute();
		if($result instanceof IStatement)
		{
			//If more than one record was returned return the array of results.
			$models = [];
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{
				$model = static::make();
				$model->_data = $row;
				$models[] = $model;
			}
			if(count($models) >= 1)
				return $models;
		}
		
		throw new ModelNotFoundException();
	}


	/**
	 * Returns all of the models in an array within the given SQL condition.
	 * @param string $statement
	 * @param mixed $order
	 * @param int|Pager $limit
	 * @param IConnection|NULL $connection
	 * @return array
	 * @throws ModelNotFoundException
	 */
	public static function findWhereStatement($statement, $order = NULL, $limit = NULL, IConnection $connection = NULL)
	{
		//Make a model instance
		$model = static::make();

		//Create the query
		$query = Select::table($model->_getTable());

		//Change connection if needed
		if(isset($connection)) $query->setConnection($connection);

		//Set WhereStatement
		$query->whereStatement($statement);

		//Set order
		if(isset($order)) $query->orderBy($order);

		//Set limit
		if(isset($limit)) $query->limit($limit);

		//Execute the query
		$result = $query->execute();
		if($result instanceof IStatement)
		{
			$models = [];
			while($row = $result->fetch(PDO::FETCH_ASSOC))
			{
				$model = static::make();
				$model->_data = $row;
				$models[] = $model;
			}
			if(count($models) >= 1)
				return $models;
		}

		throw new ModelNotFoundException();
	}

	/**
	 * Delete the model from the database.
	 * @param bool $hardDelete
	 * @return bool
	 */
	public function drop($hardDelete = false)
	{
		if($this->_softDelete == false || $hardDelete == true)
		{
			$query = Query::delete($this->_getTable(), $this->getConnection())
				->whereEqual($this->_primaryKey, $this->_data[$this->_primaryKey]);
		}
		else
		{
			$data = [$this->_softDeleteField = new \DateTime('now')];
			$query = Query::update($this->_getTable(), $data, $this->getConnection())
				->whereEqual($this->_primaryKey, $this->_data[$this->_primaryKey]);
		}

		//Execute the query and return the result
		return $query->execute();
	}

	//----------------------------------------QUERY FUNCTIONS----------------------------------------

	/**
	 * Perform a query on a model. If no query is specified then a select query is created.
	 * @param Query $baseQuery
	 * @return ModelQuery
	 */
	public static function query($baseQuery = NULL) : ModelQuery
	{
		if(isset($baseQuery))
			$query = ModelQuery::create(new static())
				->setQueryObject($baseQuery);
		else
			$query = new ModelSelectQuery(new static());
		return $query;
	}

	/**
	 * Perform a SELECT query on the models.
	 * @return ModelSelectQuery
	 */
	public static function select() : ModelSelectQuery
	{
		$query = new ModelSelectQuery(new static());
		return $query;
	}
}