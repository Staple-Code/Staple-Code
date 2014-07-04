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
abstract class Staple_Model
{
	/**
	 * 
	 * @param array $options
	 */
	function __construct(array $options = NULL)
	{
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
        foreach ($options as $key => $value)
        {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
        return $this;
    }
}

?>