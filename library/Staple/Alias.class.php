<?php
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

		//Form Class Aliases
		'ButtonElement'					=>	'\\Staple\\Form\\ButtonElement',
		'CheckboxElement'				=>	'\\Staple\\Form\\CheckboxElement',
		'CheckboxGroup'					=>	'\\Staple\\Form\\CheckboxGroup',
		'FieldElement'					=>	'\\Staple\\Form\\FieldElement',
		'FieldFilter'					=>	'\\Staple\\Form\\FieldFilter',
		'FieldValidator'				=>	'\\Staple\\Form\\FieldValidator',
		'FileElement'					=>	'\\Staple\\Form\\FileElement',
		'HiddenElement'					=>	'\\Staple\\Form\\HiddenElement',
		'ImageElement'					=>	'\\Staple\\Form\\ImageElement',
		'PasswordElement'				=>	'\\Staple\\Form\\PasswordElement',
		'RadioGroup'					=>	'\\Staple\\Form\\RadioGroup',
		'SelectElement'					=>	'\\Staple\\Form\\SelectElement',
		'SubmitElement'					=>	'\\Staple\\Form\\SubmitElement',
		'TextareaElement'				=>	'\\Staple\\Form\\TextareaElement',
		'TextElement'					=>	'\\Staple\\Form\TextElement',

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

		//Exception Class Aliases
		'PageNotFoundException'			=>	'\\Staple\Exception\PageNotFoundException',

		//Legacy Class Names
		'Staple_Auth'					=>	'\\Staple\\Auth',
		'Staple_Autoload'				=>	'\\Staple\\Autoload',
		'Staple_Config'					=>	'\\Staple\\Config',
		'Staple_AD'						=>	'\\Staple\\ActiveDirectory',
		'Staple_ADAuthAdapter'			=>	'\\Staple\\ActiveDirectoryAuthAdapter',
		'Staple_AuthAdapter'			=>	'\\Staple\\AuthAdapter',
		'Staple_AuthController'			=>	'\\Staple\\AuthContoller',
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
		'Staple_Util'					=>	'\\Staple\\Util',
		'Staple_View'					=>	'\\Staple\\View',

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