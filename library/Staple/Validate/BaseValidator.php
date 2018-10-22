<?php
/**
 * Abstract base class for form element validators
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
 */
namespace Staple\Validate;

abstract class BaseValidator implements IValidator
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
	protected $userMessage;
	
	/**
	 * Default Constructor. Supports and optional user defined error message.
	 * @param string $userMessage
	 */
	public function __construct(string $userMessage = NULL)
	{
		if(isset($userMessage))
		{
			$this->setUserErrorMessage($userMessage);
		}
	}
	
	/**
	 * Factory function to create objects.
	 * @param string $userMessage
	 * @return BaseValidator
	 */
	public static function create(string $userMessage = NULL): IValidator
	{
		return new static($userMessage);
	}
	
	/**
	 * Clears all the errors in the errors array.
	 */
	public function clearErrors(): IValidator
	{
		$this->error = array();
		return $this;
	}

	/**
	 * Adds a custom error or adds the default error to the errors array.
	 * @param string $error
	 * @return $this
	 */
	public function addError(string $error = null): IValidator
	{
		if(isset($error))
		{
			$pushError = $error;
		}
		elseif(isset($this->userMessage))
		{
			$pushError = $this->userMessage;
		}
		else
		{
			$pushError = static::DEFAULT_ERROR;
		}

        if(array_search($pushError, $this->error) === false)
		    array_push($this->error, $pushError);
		return $this;
	}
	/**
	 * @return string $userMessage
	 */
	public function getErrorsAsString(): string
	{
		$eString = implode("\n", $this->error);
		return $eString;
	}

	/**
	 * @param string $userMessage
     * @return static
	 */
	public function setUserErrorMessage(string $userMessage): IValidator
	{
		$this->userMessage = $userMessage;
		return $this;
	}

	/**
	 * Return the errors array
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->error;
	}
	
	/**
	 * Returns the name of the validator
	 * @return string
	 */
	public function getName(): string
	{
		$c = get_class($this);
		$c = str_replace('Staple_Form_Validate_', '', $c);
		return $c;
	}
}