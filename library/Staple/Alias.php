<?php
/**
 * A class for aliasing the namespaced Staple classes.
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

class Alias
{
	protected static $class_map = [];
	
	/**
	 * Checks for an aliased class in the class map. Returns the full name of the class
	 * @param string $alias
	 * @return string | NULL
	 */
	public static function checkAlias($alias)
	{
		//Get the namespaces
		$namespaces = explode('\\',$alias);

		//Check to see if we are in the staple namespace
		if($namespaces[0] == __NAMESPACE__)
		{
			$alias = array_pop($namespaces);
		}

		//Check for a matching alias in the alias map.
		if(isset(static::$class_map[$alias]))
		{
			return static::$class_map[$alias];
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * Add an alias to the class mapping. Returns true on success, false on failure of if the mapping already exists.
	 * @param string $alias
	 * @param string $class
	 * @return boolean
	 */
	public static function addAlias($alias, $class)
	{
		if(!isset(static::$class_map[$alias]))
		{
			static::$class_map[$alias] = $class;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Set the class alias in the application.
	 * @param string $alias
	 * @param boolean $autoload
	 * @return boolean
	 */
	public static function load($alias, $autoload = true)
	{
		//Check for the class alias
		$class = static::checkAlias($alias);

		//Check for a Staple Namespace
		if(substr($class,0,1) == '\\')
			$class = substr($class,1);

		//Check that we are not trying to double declare a class
		if($class == $alias)
			return true;

		if(!is_null($class))
			if(!class_exists($alias))
				return class_alias($class, $alias, $autoload);

		//Return false otherwise
		return false;
	}

	/**
	 * return the entire class map array
	 * @return array
	 */
	public static function getClassMap()
	{
		return self::$class_map;
	}
}