<?php
namespace Staple;

class Alias
{
	protected static $class_map = array(
		'ActiveDirectory'				=>	'\\Staple\\ActiveDirectory',
		'ActiveDirectoryAuthAdapter'	=>	'\\Staple\\ActiveDirectoryAuthAdapter',
		'Alias'							=>	'\\Staple\\Alias',
		'Auth'							=>	'\\Staple\\Auth',
		'AuthAdapter'					=>	'\\Staple\\AuthAdapter',
		'AuthController'				=>	'\\Staple\\AuthContoller',
		'Autoload'						=>	'\\Staple\\Autoload',
		'Config'						=>	'\\Staple\\Config',
		'Controller'					=>	'\\Staple\\Controller',
		'DB'							=>	'\\Staple\\DB',
		'DBAuthAdapter'					=>	'\\Staple\\DBAuthAdapter',
		'Dev'							=>	'\\Staple\\Dev',
		'Encrypt'						=>	'\\Staple\\Encrypt',
		'Error'							=>	'\\Staple\\Error',
		'Form'							=>	'\\Staple\\Form\\Form',
		'Image'							=>	'\\Staple\\Image',
		'Layout'						=>	'\\Staple\\Layout',
		'Link'							=>	'\\Staple\\Link',
		'Mail'							=>	'\\Staple\\Mail',
		'Main'							=>	'\\Staple\\Main',
		'Model'							=>	'\\Staple\\Model',
		'Pager'							=>	'\\Staple\\Pager',
		'Registry'						=>	'\\Staple\\Registry',
		'Request'						=>	'\\Staple\\Request',
		'Route'							=>	'\\Staple\\Route',
		'Script'						=>	'\\Staple\\Script',
		'Util'							=>	'\\Staple\\Util',
		'View'							=>	'\\Staple\\View',
	);
	
	/**
	 * Checks for an aliased class in the class map.
	 * @param string $alias
	 * @return string | NULL
	 */
	public static function checkAlias($alias)
	{
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
	 * @param boolean $load
	 * @return boolean
	 */
	public static function load($alias, $autoload = true)
	{
		$class = static::checkAlias($alias);
		if(!is_null($class))
		{
			return class_alias($class,$alias, $autoload);
		}
		else
		{
			return false;
		}
	}
}

?>