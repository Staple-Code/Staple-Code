<?php

/** 
 * Basic application registry class;
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
 * 
 */
namespace Staple;

use Exception;
use Staple\Session\Session;

class Registry
{
	protected static $store = array();

	/**
	 * Returns a booleon if the specified key exists in the store.
	 * @param string $key
	 * @return bool
	 */
	public static function isValid($key)
	{
		$data = Session::register($key);
		if(!is_null($data))
		{
			self::$store[$key] = $data;
			return true;
		}
		return array_key_exists($key, self::$store);
	}
	
	/**
	 * Store a value or object in the registry.
	 * @param string $key
	 * @return mixed|NULL
	 */
	public static function get($key)
	{
		if(array_key_exists($key, self::$store))
			return self::$store[$key];
		else
		{
			$data = Session::register($key);
			if(isset($data))
			{
				self::$store[$key] = $data;
				return $data;
			}
			else
				return null;
		}
	}
	
	/**
	 * Store a value or object in the registry.
	 * @param string $key
	 * @param mixed $obj
	 * @param bool $storeInSession
	 */
	public static function set($key, $obj, $storeInSession = true)
	{
		//We can't store resources in the session.
		if(!is_resource($obj) && $storeInSession === true)
		{
			Session::register($key, $obj);
		}
		
		//Store the value locally as well.
		self::$store[$key] = $obj;
	}
	
	public function __clone()
    {
        throw new Exception('Clone is not allowed.');
    }
}
