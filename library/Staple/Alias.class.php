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

		'PageNotFoundException'			=>	'\\Staple\Exception\PageNotFoundException',
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