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

use \Exception;

abstract class Model implements \JsonSerializable, \ArrayAccess, \Iterator
{
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
	protected $_properties = array();
	/**
	 * A database connection object that the model uses
	 * @var DB
	 */
	protected $_modelDB;
	/**
	 * 
	 * @param array $options
	 */
	public function __construct(array $options = NULL)
	{
		//@todo add a pluralization/snake_case conversion here
		if(substr(__CLASS__,strlen(__CLASS__)-5,strlen(__CLASS__)) == 'Model')
			$this->_table = str_replace('Model', '', __CLASS__);
		else
			$this->_table = __CLASS__;
		
		if (is_array($options))
		{
            $this->_options($options);
        }
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
        if (method_exists($this, $method))
        {
            //Use the setter built onto the object
            $this->$method($value);
        }
        else
        {
            //Set the property dynamically
            $this->_properties[$name] = $value;
        }
    }
 
    /**
     * 
     * Allows dynamic calling of Model properties
     * @param string $name
     * @throws Exception
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method))
        {
            return $this->$method();
        }
        elseif(array_key_exists($name,$this->_properties))
        {
            return $this->_properties[$name];
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
        return isset($this->_properties[$name]);
    }

    /**
     * Unset a dynamic property of the model
     * @param $name
     */
    public function __unset($name)
    {
        if(isset($this->_properties[$name]))
            unset($this->_properties[$name]);
    }
    
    /**
     * Dynamically call properties without having to create getters and setters.
     */
    public function __call($name , array $arguments)
    {
    	//@todo incomplete function 
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
     */
    public function _options($options)
    {
        foreach ($options as $key=>$value)
        {
        	$method = 'set' . ucfirst($key);
            $method2 = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($this, $method))
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
	 * 
	 */
	public function jsonSerialize()
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current()
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key()
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next()
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid()
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset)
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset)
	{
		// TODO Auto-generated method stub
		
	}

	/**
	 * @return DB $_modelDB
	 */
	public function getModelDB()
	{
		if(isset($this->_modelDB))		//Return the specified model connection
		{
			return $this->_modelDB;
		}
		else							//Return the default connection
		{
			return DB::get();
		}
	}

	/**
	 * @param DB $_modelDB
	 */
	public function setModelDB(DB $_modelDB)
	{
		$this->_modelDB = $_modelDB;
		return $this;
	}

	/**
	 * Save the model to the database
	 * @return boolean
	 */
	public function save()
	{
		//@todo incomplete function
		return false;
	}
	
	/**
	 * Return an instance of the model from the primary key.
	 * @param int $id
	 */
	public static function get($id)
	{
		//@todo incomplete function
	}

	public static function getAll()
	{
		//@todo incomplete function
	}
	
	public static function getWhereEqual($column, $value)
	{
		//@todo incomplete function
	}
	
	public static function getWhereNull($column)
	{
		//@todo incomplete function
	}
	
	public static function getWhereIn($column, array $values)
	{
		//@todo incomplete function
	}
	
	public static function getWhereStatement($statement)
	{
		//@todo incomplete function
	}
}

?>