<?php
/** 
 * A container to reference config settings without having to re-read the config file.
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

/**
* Added to include Registry once so Config class will load with out autoloader being started.
*/
require_once STAPLE_ROOT.'Registry.class.php';

class Config extends Registry
{
	protected static $read = false;
	
	/**
	 * Disable construction of this object.
	 */
	private function __construct(){}
	
	/**
	 * Get a config set by header.
	 * @param string $name
	 */
	public function __get($name)
	{
		if(!self::$read)
		{
			self::read();
		}
		if(array_key_exists($name, self::$store))
		{
			return self::$store[$name];
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Setting config values during runtime are not allowed.
	 * @throws Exception
	 */
	public function __set($name,$value)
	{
		throw new Exception('Config changes are not allowed at execution',Error::APPLICATION_ERROR);
	}
	
	/**
	 * Get a config set by header.
	 * @param string $name
	 * @return array
	 */
	public static function get($name)
	{
		if(!self::$read)
		{
			self::read();
		}
		if(array_key_exists($name, self::$store))
		{
			return self::$store[$name];
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Returns the entire config array.
	 * @return array
	 */
	public static function getAll()
	{
		return self::$store;
	}
	
	/**
	 * Returns a single value from the configuration file.
	 * 
	 * @param string $set
	 * @param string $key
	 * @return mixed
	 */
	public static function getValue($set,$key)
	{
		if(!self::$read)
		{
			self::read();
		}
		if(array_key_exists($set, self::$store))
		{
			if(array_key_exists($key, self::$store[$set]))
			{
				return self::$store[$set][$key];
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * Setting config values during runtime are not allowed.
	 * @throws Exception
	 */
	public static function set($name,$value,$storeInSession = false)
	{
		throw new Exception('Config changes are not allowed at execution', Error::APPLICATION_ERROR);
	}
	
	/**
	 * Sets a configuration value at runtime. Returns a true or false on success or failure.
	 * 
	 * @param string $set
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public static function setValue($set,$key,$value)
	{
		if(!self::$read)
		{
			self::read();
		}
		if(array_key_exists($set, self::$store))
		{
			if(array_key_exists($key, self::$store[$set]))
			{
				self::$store[$set][$key] = $value;
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * Read and store the application.ini config file.
	 */
	private static function read()
	{
		if(defined('CONFIG_ROOT'))
		{
			$settings = array();
			if(file_exists(CONFIG_ROOT.'application.ini'))
			{
				self::$store = parse_ini_file(CONFIG_ROOT.'application.ini',true);
			}
		}
		self::$read = true;
	}
}