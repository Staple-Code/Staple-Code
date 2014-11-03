<?php

/** 
 * A parent class for models in STAPLE.
 * 
 * @author Ironpilot
 * @copyright Copywrite (c) 2011, STAPLE CODE
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

abstract class Model
{
	protected $_modelDB;
	/**
	 * 
	 * @param array $options
	 */
	function __construct(array $options = array())
	{
		if (count($options) > 0)
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
        if (!method_exists($this, $method))
        {
            throw new Exception('Model does not contain specified property');
        }
        $this->$method($value);
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
        if (!method_exists($this, $method)) 
        {
            throw new Exception('Model does not contain specified property');
        }
        return $this->$method();
    }
 
    /**
     * 
     * Sets model properties supplied via an associative array.
     * @param array $options
     */
    public function _options(array $options)
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
	 * @return Staple_DB $_modelDB
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
	 * @param Staple_DB $_modelDB
	 */
	public function setModelDB(DB $_modelDB)
	{
		$this->_modelDB = $_modelDB;
		return $this;
	}

}

?>