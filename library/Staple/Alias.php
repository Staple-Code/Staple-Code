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
	protected static $class_map = array(
		//Primary Staple Object Aliases
		'ActiveDirectory'					=>	'\\Staple\\ActiveDirectory',
		'Alias'							=>	'\\Staple\\Alias',
		'Autoload'						=>	'\\Staple\\Autoload',
		'Config'						=>	'\\Staple\\Config',
		'Controller'						=>	'\\Staple\\Controller\\Controller',
		'DB'							=>	'\\Staple\\DB',
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
		'Response'						=>	'\\Staple\\Response',
		'Route'							=>	'\\Staple\\Route',
		'Utility'						=>	'\\Staple\\Utility',
		'View'							=>	'\\Staple\\View',

		//Auth
		'Auth'						=>	'\\Staple\\Auth\\Auth',
		'AuthAdapter'					=>	'\\Staple\\Auth\\AuthAdapter',
		'ActiveDirectoryAuthAdapter'			=>	'\\Staple\\Auth\\ActiveDirectoryAuthAdapter',
		'DBAuthAdapter'					=>	'\\Staple\\Auth\\DBAuthAdapter',
		'OAuthAdapter'					=>	'\\Staple\\Auth\\OAuthAdapter',

		//Form Class Aliases
		'ButtonElement'					=>	'\\Staple\\Form\\ButtonElement',
		'CheckboxElement'				=>	'\\Staple\\Form\\CheckboxElement',
		'CheckboxGroupElement'				=>	'\\Staple\\Form\\CheckboxGroupElement',
		'FieldElement'					=>	'\\Staple\\Form\\FieldElement',
		'FieldFilter'					=>	'\\Staple\\Form\\FieldFilter',
		'FileElement'					=>	'\\Staple\\Form\\FileElement',
		'HiddenElement'					=>	'\\Staple\\Form\\HiddenElement',
		'ImageElement'					=>	'\\Staple\\Form\\ImageElement',
		'PasswordElement'				=>	'\\Staple\\Form\\PasswordElement',
		'RadioElement'					=>	'\\Staple\\Form\\RadioElement',
		'SelectElement'					=>	'\\Staple\\Form\\SelectElement',
		'SubmitElement'					=>	'\\Staple\\Form\\SubmitElement',
		'TextareaElement'				=>	'\\Staple\\Form\\TextareaElement',
		'TextElement'					=>	'\\Staple\\Form\TextElement',

        //Field Validator Class Aliases
        'DependentFieldValidator'			=>	'\\Staple\\Form\\Validate\\DependentFieldValidator',
        'IdenticalFieldValidator'			=>	'\\Staple\\Form\\Validate\\IdenticalFieldValidator',

		//Validator Class Aliases
		'AlnumValidator'				=>	'\\Staple\\Validate\\AlnumValidator',
		'BetweenFloatValidator'				=>	'\\Staple\\Validate\\BetweenFloatValidator',
		'BetweenValidator'				=>	'\\Staple\\Validate\BetweenValidator',
		'DateValidator'					=>	'\\Staple\\Validate\\DateValidator',
		'EmailValidator'				=>	'\\Staple\\Validate\\EmailValidator',
		'EqualValidator'				=>	'\\Staple\\Validate\\EqualValidator',
		'FloatValidator'				=>	'\\Staple\\Validate\\FloatValidator',
		'InArrayValidator'				=>	'\\Staple\\Validate\\InArrayValidator',
		'LengthValidator'				=>	'\\Staple\\Validate\\LengthValidator',
		'NotEqualValidator'				=>	'\\Staple\\Validate\\NotEqualValidator',
		'NumericValidator'				=>	'\\Staple\\Validate\\NumericValidator',
		'PhoneValidator'				=>	'\\Staple\\Validate\\PhoneValidator',
		'RegexValidator'				=>	'\\Staple\\Validate\\RegexValidator',
		'UploadedFileValidator'				=>	'\\Staple\\Validate\\UploadedFileValidator',
		'ZipValidator'					=>	'\\Staple\\Validate\\ZipValidator',

		//Filter Class Aliases
		'BaseNameFilter'				=>	'\\Staple\\Form\\Filter\\BaseNameFilter',
		'IntegerFilter'					=>	'\\Staple\\Form\\Filter\\IntegerFilter',
		'PhoneFormatFilter'				=>	'\\Staple\\Form\\Filter\\PhoneFormatFilter',
		'TagsFilter'					=>	'\\Staple\\Form\\Filter\\TagsFilter',
		'ToDateTimeFilter'				=>	'\\Staple\\Form\\Filter\\ToDateTimeFilter',
		'ToLowerFilter'					=>	'\\Staple\\Form\\Filter\\ToLowerFilter',
		'ToUpperFilter'					=>	'\\Staple\\Form\\Filter\\ToUpperFilter',
		'TrimFilter'					=>	'\\Staple\\Form\\Filter\\TrimFilter',

		//Data Class Aliases
		'DoubleLinkedList'			=>	'\\Staple\\Data\\DoubleLinkedList',
		'LinkedList'				=>	'\\Staple\\Data\\LinkedList',
		'LinkedListNode'			=>	'\\Staple\\Data\\LinkedListNode',
		'LinkedListNodeDouble'			=>	'\\Staple\\Data\\LinkedListNodeDouble',
		'Queue'					=>	'\\Staple\\Data\\Queue',
		'Stack'					=>	'\\Staple\\Data\\Stack',

		//Query Builder Classes
		'Connection'					=>	'\\Staple\\Query\\Connection',
		'QueryCondition'					=>	'\\Staple\\Query\\Condition',
		'QueryDataSet'						=>	'\\Staple\\Query\\DataSet',
		'Delete'						=>	'\\Staple\\Query\\Delete',
		'Insert'						=>	'\\Staple\\Query\\Insert',
		'InsertMultiple'					=>	'\\Staple\\Query\\InsertMultiple',
		'MockConnection'				=>	'\\Staple\\Query\\MockConnection',
		'MockStatement'					=>	'\\Staple\\Query\\MockStatement',
		'QueryJoin'						=>	'\\Staple\\Query\\Join',
		'Query'							=>	'\\Staple\\Query\\Query',
		'Select'						=>	'\\Staple\\Query\\Select',
		'Statement'						=>	'\\Staple\\Query\\Statement',
		'Union'							=>	'\\Staple\\Query\\Union',
		'Update'						=>	'\\Staple\\Query\\Update',

		//Exception Class Aliases
		'PageNotFoundException'			=>	'\\Staple\\Exception\\PageNotFoundException',
		'RoutingException'			=>	'\\Staple\\Exception\\RoutingException',

		//Trait Aliases
		'Helpers'				=>	'\\Staple\\Traits\\Helpers',
		'Singleton'				=>	'\\Staple\\Traits\\Singleton',

		//Form Element View Adapters
		'FormElementViewAdapter'		=> '\\Staple\\Form\\ViewAdapters\\ElementViewAdapter',
		'FormFoundationViewAdapter'		=> '\\Staple\\Form\\ViewAdapters\\FoundationViewAdapter',
		'FormBootstrapViewAdapter'		=> '\\Staple\\Form\\ViewAdapters\\BootstrapViewAdapter',

		//Session Classes
		'Session'				=>	'\\Staple\\Session\\Session',
		'SessionHandler'			=>	'\\Staple\\Session\\Handler',
		'SessionFileHandler'			=>	'\\Staple\\Session\\FileHandler',
		'SessionDatabaseHandler'		=>	'\\Staple\\Session\\DatabaseHandler',
		'SessionRedisHandler'			=>	'\\Staple\\Session\\RedisHandler',

		//Controller Classes
		'RestfulController'			=>	'\\Staple\\Controller\RestfulController',
	);
	
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