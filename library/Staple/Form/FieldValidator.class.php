<?php
namespace Staple\Form;

use Staple;

abstract class FieldValidator
{
	/**
	 * This class constant defines the default error message for the validator. Each child class should customize this value.
	 * @var string
	 */
	const DEFAULT_ERROR = 'An error occurred while validating the field.';
	/**
	 * An array that holds the default error messages for the validator.
	 * @var array
	 */
	protected $error = array();
	/**
	 * A string that holds the user-defined error message when validation fails.
	 * @var string
	 */
	protected $usermsg;
	
	/**
	 * Default Constructor. Supports and optional user defined error message.
	 * @param string $usermsg
	 */
	public function __construct($usermsg = NULL)
	{
		if(isset($usermsg))
		{
			$this->setUserErrorMessage($usermsg);
		}
	}
	
	/**
	 * Factory function to create objects.
	 * @param string $usermsg
	 */
	public static function Create($usermsg = NULL)
	{
		return new static($usermsg);
	}
	
	/*
	public function getError()
	{
		return $this->error;
	}*/
	
	/**
	 * Gets the error message for client side checking.
	 */
	public function clientJSError()
	{
		if(isset($this->usermsg))
		{
			return $this->usermsg;
		}
		else 
		{
			return static::DEFAULT_ERROR;
		}
	}
	
	/**
	 * Clears all the errors in the errors array.
	 */
	public function clearErrors()
	{
		$this->error = array();
		return $this;
	}
	
	/**
	 * Adds a custom error or adds the default error to the errors array.
	 * @param string $err
	 */
	public function addError($err = NULL)
	{
		if(isset($err))
		{
			$this->error[] = $err;
		}
		elseif(isset($this->usermsg))
		{
			$this->error[] = $this->usermsg;
		}
		else
		{
			$this->error[] = static::DEFAULT_ERROR;
		}
		return $this;
	}
	/**
	 * @return the $usermsg
	 */
	public function getErrorsAsString()
	{
		$eString = implode("\n", $this->error);
		if(isset($this->usermsg))
		{
			$eString = $this->usermsg."\n".$eString;
		}
		return $eString;
	}

	/**
	 * @param string $usermsg
	 */
	public function setUserErrorMessage($usermsg)
	{
		$this->usermsg = $usermsg;
	}

	/**
	 * Return the errors array
	 * @return array
	 */
	public function getErrors()
	{
		return $this->error;
	}
	
	/**
	 * Returns the name of the validator
	 * @return string
	 */
	public function getName()
	{
		$c = get_class($this);
		$c = str_replace('Staple_Form_Validate_', '', $c);
		return $c;
	}
	
	/**
	 * Function for client side form checking. Must be overridden in the child class.
	 * @param string $fieldType
	 * @param Staple_Form_Element $field
	 */
	public function clientJS($fieldType, Staple_Form_Element $field)
	{
		return '';
	}
	
	/**
	 * Function for client side form checking. Must be overridden in the child class. This one is specific to JQuery.
	 * @param string $fieldType
	 * @param Staple_Form_Element $field
	 */
	public function clientJQuery($fieldType, Staple_Form_Element $field)
	{
		return '';
	}
	
	/**
	 * 
	 * Returns a boolean true or false on success or failure of the validation check.
	 * @param mixed $data
	 * @return bool
	 */
	abstract public function check($data);
}