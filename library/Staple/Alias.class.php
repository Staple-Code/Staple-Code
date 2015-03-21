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
		'ActiveDirectory'				=>	'\\Staple\\ActiveDirectory',
		'ActiveDirectoryAuthAdapter'	=>	'\\Staple\\ActiveDirectoryAuthAdapter',
		'Alias'							=>	'\\Staple\\Alias',
		'Auth'							=>	'\\Staple\\Auth',
		'AuthAdapter'					=>	'\\Staple\\AuthAdapter',
		'AuthController'				=>	'\\Staple\\AuthController',
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
		'Utility'						=>	'\\Staple\\Utility',
		'View'							=>	'\\Staple\\View',

		//Legacy Class Names
		'Staple_Auth'					=>	'\\Staple\\Auth',
		'Staple_Autoload'				=>	'\\Staple\\Autoload',
		'Staple_AD'						=>	'\\Staple\\ActiveDirectory',
		'Staple_ADAuthAdapter'			=>	'\\Staple\\ActiveDirectoryAuthAdapter',
		'Staple_AuthAdapter'			=>	'\\Staple\\AuthAdapter',
		'Staple_AuthController'			=>	'\\Staple\\AuthController',
		'Staple_Config'					=>	'\\Staple\\Config',
		'Staple_Controller'				=>	'\\Staple\\Controller',
		'Staple_DB'						=>	'\\Staple\\DB',
		'Staple_DBAuthAdapter'			=>	'\\Staple\\DBAuthAdapter',
		'Staple_Dev'					=>	'\\Staple\\Dev',
		'Staple_Encrypt'				=>	'\\Staple\\Encrypt',
		'Staple_Error'					=>	'\\Staple\\Error',
		'Staple_Form'					=>	'\\Staple\\Form\\Form',
		'Staple_Image'					=>	'\\Staple\\Image',
		'Staple_Layout'					=>	'\\Staple\\Layout',
		'Staple_Link'					=>	'\\Staple\\Link',
		'Staple_Mail'					=>	'\\Staple\\Mail',
		'Staple_Main'					=>	'\\Staple\\Main',
		'Staple_Model'					=>	'\\Staple\\Model',
		'Staple_Pager'					=>	'\\Staple\\Pager',
		'Staple_Registry'				=>	'\\Staple\\Registry',
		'Staple_Request'				=>	'\\Staple\\Request',
		'Staple_Route'					=>	'\\Staple\\Route',
		'Staple_Script'					=>	'\\Staple\\Script',
		'Staple_Util'					=>	'\\Staple\\Utility',
		'Staple_View'					=>	'\\Staple\\View',

		//Form Class Aliases
		'ButtonElement'					=>	'\\Staple\\Form\\ButtonElement',
		'CheckboxElement'				=>	'\\Staple\\Form\\CheckboxElement',
		'CheckboxGroupElement'			=>	'\\Staple\\Form\\CheckboxGroupElement',
		'FieldElement'					=>	'\\Staple\\Form\\FieldElement',
		'FieldFilter'					=>	'\\Staple\\Form\\FieldFilter',
		'FieldValidator'				=>	'\\Staple\\Form\\FieldValidator',
		'FileElement'					=>	'\\Staple\\Form\\FileElement',
		'HiddenElement'					=>	'\\Staple\\Form\\HiddenElement',
		'ImageElement'					=>	'\\Staple\\Form\\ImageElement',
		'PasswordElement'				=>	'\\Staple\\Form\\PasswordElement',
		'RadioElement'					=>	'\\Staple\\Form\\RadioElement',
		'SelectElement'					=>	'\\Staple\\Form\\SelectElement',
		'SubmitElement'					=>	'\\Staple\\Form\\SubmitElement',
		'TextareaElement'				=>	'\\Staple\\Form\\TextareaElement',
		'TextElement'					=>	'\\Staple\\Form\TextElement',

		//Legacy Form Classes
		'Staple_Form_ButtonElement'		=>	'\\Staple\\Form\\ButtonElement',
		'Staple_Form_CheckboxElement'	=>	'\\Staple\\Form\\CheckboxElement',
		'Staple_Form_CheckboxGroup'		=>	'\\Staple\\Form\\CheckboxGroupElement',
		'Staple_Form_Element'			=>	'\\Staple\\Form\\FieldElement',
		'Staple_Form_Filter'			=>	'\\Staple\\Form\\FieldFilter',
		'Staple_Form_Validator'			=>	'\\Staple\\Form\\FieldValidator',
		'Staple_Form_FileElement'		=>	'\\Staple\\Form\\FileElement',
		'Staple_Form_HiddenElement'		=>	'\\Staple\\Form\\HiddenElement',
		'Staple_Form_ImageElement'		=>	'\\Staple\\Form\\ImageElement',
		'Staple_Form_PasswordElement'	=>	'\\Staple\\Form\\PasswordElement',
		'Staple_Form_RadioGroup'		=>	'\\Staple\\Form\\RadioElement',
		'Staple_Form_SelectElement'		=>	'\\Staple\\Form\\SelectElement',
		'Staple_Form_SubmitElement'		=>	'\\Staple\\Form\\SubmitElement',
		'Staple_Form_TextareaElement'	=>	'\\Staple\\Form\\TextareaElement',
		'Staple_Form_TextElement'		=>	'\\Staple\\Form\TextElement',

		//Validator Class Aliases
		'AlnumValidator'				=>	'\\Staple\\Form\\Validate\AlnumValidator',
		'BetweenFloatValidator'			=>	'\\Staple\\Form\\Validate\BetweenFloatValidator',
		'BetweenValidator'				=>	'\\Staple\\Form\\Validate\BetweenValidator',
		'DateValidator'					=>	'\\Staple\\Form\\Validate\DateValidator',
		'EmailValidator'				=>	'\\Staple\\Form\\Validate\EmailValidator',
		'EqualValidator'				=>	'\\Staple\\Form\\Validate\EqualValidator',
		'FloatValidator'				=>	'\\Staple\\Form\\Validate\FloatValidator',
		'IdenticalFieldValidator'		=>	'\\Staple\\Form\\Validate\IdenticalFieldValidator',
		'InArrayValidator'				=>	'\\Staple\\Form\\Validate\InArrayValidator',
		'LengthValidator'				=>	'\\Staple\\Form\\Validate\LengthValidator',
		'NotEqualValidator'				=>	'\\Staple\\Form\\Validate\NotEqualValidator',
		'NumericValidator'				=>	'\\Staple\\Form\\Validate\NumericValidator',
		'PhoneValidator'				=>	'\\Staple\\Form\\Validate\PhoneValidator',
		'RegexValidator'				=>	'\\Staple\\Form\\Validate\RegexValidator',
		'UploadedFileValidator'			=>	'\\Staple\\Form\\Validate\UploadedFileValidator',
		'ZipValidator'					=>	'\\Staple\\Form\\Validate\ZipValidator',

		//Legacy Validator Class Aliases
		'Staple_Form_Validate_Alnum'			=>	'\\Staple\\Form\\Validate\AlnumValidator',
		'Staple_Form_Validate_BetweenFloat'		=>	'\\Staple\\Form\\Validate\BetweenFloatValidator',
		'Staple_Form_Validate_Between'			=>	'\\Staple\\Form\\Validate\BetweenValidator',
		'Staple_Form_Validate_Date'				=>	'\\Staple\\Form\\Validate\DateValidator',
		'Staple_Form_Validate_Email'			=>	'\\Staple\\Form\\Validate\EmailValidator',
		'Staple_Form_Validate_Equal'			=>	'\\Staple\\Form\\Validate\EqualValidator',
		'Staple_Form_Validate_Float'			=>	'\\Staple\\Form\\Validate\FloatValidator',
		'Staple_Form_Validate_IdenticalField'	=>	'\\Staple\\Form\\Validate\IdenticalFieldValidator',
		'Staple_Form_Validate_InArray'			=>	'\\Staple\\Form\\Validate\InArrayValidator',
		'Staple_Form_Validate_Length'			=>	'\\Staple\\Form\\Validate\LengthValidator',
		'Staple_Form_Validate_NotEqual'			=>	'\\Staple\\Form\\Validate\NotEqualValidator',
		'Staple_Form_Validate_Numeric'			=>	'\\Staple\\Form\\Validate\NumericValidator',
		'Staple_Form_Validate_Phone'			=>	'\\Staple\\Form\\Validate\PhoneValidator',
		'Staple_Form_Validate_Regex'			=>	'\\Staple\\Form\\Validate\RegexValidator',
		'Staple_Form_Validate_UploadedFile'		=>	'\\Staple\\Form\\Validate\UploadedFileValidator',
		'Staple_Form_Validate_Zip'				=>	'\\Staple\\Form\\Validate\ZipValidator',

		//Filter Class Aliases
		'BaseNameFilter'				=>	'\\Staple\\Form\\Filter\BaseNameFilter',
		'IntegerFilter'					=>	'\\Staple\\Form\\Filter\IntegerFilter',
		'PhoneFormatFilter'				=>	'\\Staple\\Form\\Filter\PhoneFormatFilter',
		'TagsFilter'					=>	'\\Staple\\Form\\Filter\TagsFilter',
		'ToDateTimeFilter'				=>	'\\Staple\\Form\\Filter\ToDateTimeFilter',
		'ToLowerFilter'					=>	'\\Staple\\Form\\Filter\ToLowerFilter',
		'ToUpperFilter'					=>	'\\Staple\\Form\\Filter\ToUpperFilter',
		'TrimFilter'					=>	'\\Staple\\Form\\Filter\TrimFilter',

		//Legacy Validator Class Aliases
		'Staple_Form_Filter_BaseName'		=>	'\\Staple\\Form\\Filter\BaseNameFilter',
		'Staple_Form_Filter_Integer'		=>	'\\Staple\\Form\\Filter\IntegerFilter',
		'Staple_Form_Filter_PhoneFormat'	=>	'\\Staple\\Form\\Filter\PhoneFormatFilter',
		'Staple_Form_Filter_Tags'			=>	'\\Staple\\Form\\Filter\TagsFilter',
		'Staple_Form_Filter_ToDateTime'		=>	'\\Staple\\Form\\Filter\ToDateTimeFilter',
		'Staple_Form_Filter_ToLower'		=>	'\\Staple\\Form\\Filter\ToLowerFilter',
		'Staple_Form_Filter_ToUpper'		=>	'\\Staple\\Form\\Filter\ToUpperFilter',
		'Staple_Form_Filter_Trim'			=>	'\\Staple\\Form\\Filter\TrimFilter',

		//Data Class Aliases
		'DoubleLinkedList'				=>	'\\Staple\\Data\\DoubleLinkedList',
		'LinkedList'					=>	'\\Staple\\Data\\LinkedList',
		'LinkedListNode'				=>	'\\Staple\\Data\\LinkedListNode',
		'LinkedListNodeDouble'			=>	'\\Staple\\Data\\LinkedListNodeDouble',
		'Queue'							=>	'\\Staple\\Data\\Queue',
		'Stack'							=>	'\\Staple\\Data\\Stack',

		//Legacy Data Class Aliases
		'Staple_Data_DoubleLinkedList'		=>	'\\Staple\\Data\\DoubleLinkedList',
		'Staple_Data_LinkedList'			=>	'\\Staple\\Data\\LinkedList',
		'Staple_Data_LinkedListNode'		=>	'\\Staple\\Data\\LinkedListNode',
		'Staple_Data_LinkedListNodeDouble'	=>	'\\Staple\\Data\\LinkedListNodeDouble',
		'Staple_Data_Queue'					=>	'\\Staple\\Data\\Queue',
		'Staple_Data_Stack'					=>	'\\Staple\\Data\\Stack',

		//Query Builder Classes
		'QueryCondition'				=>	'\\Staple\\Query\\Condition',
		'QueryDataSet'					=>	'\\Staple\\Query\\DataSet',
		'Delete'						=>	'\\Staple\\Query\\Delete',
		'Insert'						=>	'\\Staple\\Query\\Insert',
		'InsertMultiple'				=>	'\\Staple\\Query\\InsertMultiple',
		'QueryJoin'						=>	'\\Staple\\Query\\Join',
		'Query'							=>	'\\Staple\\Query\\Query',
		'Select'						=>	'\\Staple\\Query\\Select',
		'Union'							=>	'\\Staple\\Query\\Union',
		'Update'						=>	'\\Staple\\Query\\Update',

		//Legacy Query Builder Classes
		'Staple_Query_Condition'				=>	'\\Staple\\Query\\Condition',
		'Staple_Query_DataSet'					=>	'\\Staple\\Query\\DataSet',
		'Staple_Query_Delete'					=>	'\\Staple\\Query\\Delete',
		'Staple_Query_Insert'					=>	'\\Staple\\Query\\Insert',
		'Staple_Query_InsertMultiple'			=>	'\\Staple\\Query\\InsertMultiple',
		'Staple_Query_Join'						=>	'\\Staple\\Query\\Join',
		'Staple_Query'							=>	'\\Staple\\Query\\Query',
		'Staple_Query_Select'					=>	'\\Staple\\Query\\Select',
		'Staple_Query_Union'					=>	'\\Staple\\Query\\Union',
		'Staple_Query_Update'					=>	'\\Staple\\Query\\Update',

		//Exception Class Aliases
		'PageNotFoundException'			=>	'\\Staple\\Exception\\PageNotFoundException',
		'RoutingException'				=>	'\\Staple\\Exception\\RoutingException',

		//Trait Aliases
		'Helpers'						=>	'\\Staple\\Traits\\Helpers',
		'Singleton'						=>	'\\Staple\\Traits\\Singleton',

		//Form Element View Adapters
		'FoundationViewAdapter'			=> '\\Staple\\Form\\ViewAdapters\\FoundationViewAdapter'

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
	public function getClassMap()
	{
		return self::$class_map;
	}
}

?>