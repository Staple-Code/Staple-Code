<?php

/** 
 * Basic application registry class;
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
 * 
 */
class Staple_Registry
{
	protected static $store = array();
	
	public static function isValid($key)
	{
		return array_key_exists($key, self::$store);
	}
	
	public static function get($key)
	{
		if(array_key_exists($key, self::$store))
			return self::$store[$key];
		else
			return false;
	}
	
	public static function set($key, $obj)
	{
		self::$store[$key] = $obj;
	}
	
	public function __clone()
    {
        throw new Exception('Clone is not allowed.');
    }
}

?>