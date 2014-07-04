<?php
/**
 * Base class for form validation.
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
abstract class Staple_Form_Validator
{
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
	
	
	public function __construct($usermsg = NULL)
	{
		if(isset($usermsg))
		{
			$this->setErrorMessage($usermsg);
		}
	}
	
	public static function Create($usermsg = NULL)
	{
		return new static($usermsg);
	}
	
	public function getError()
	{
		return $this->error;
	}
	/**
	 * 
	 * Returns a boolean true or false on success or failure of the validation check.
	 * @param mixed $data
	 * @return bool
	 */
	abstract public function check($data);
	
	public function clientJS($fieldType, Staple_Form_Element $field)
	{
		return '';
	}
	public function clientJQuery($fieldType, Staple_Form_Element $field)
	{
		return '';
	}
	
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
	
	public function addError($err)
	{
		$this->error[] = $err;
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
	public function setErrorMessage($usermsg)
	{
		$this->usermsg = $usermsg;
	}

	public function getErrors()
	{
		if(isset($this->usermsg))
		{
			return array_merge($this->error,array('usermsg'=>$this->usermsg));	
		}
		else 
		{
			return $this->error;
		}
	}
	
	public function getName()
	{
		$c = get_class($this);
		$c = str_replace('Staple_Form_Validate_', '', $c);
		return $c;
	}
}