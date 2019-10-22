<?php
/** 
 * Validates the length of a form field.
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

class LengthValidator extends BaseValidator
{
	const DEFAULT_ERROR = 'Field does not meet length requirements.';
	const MIN_LENGTH_ERROR = 'Minimum length not met.';
	const MAX_LENGTH_ERROR = 'Maximum length exceeded.';
	protected $min = 0;
	protected $max;
	
	/**
	 * Accepts a maximum length to validate against. Also accepts an optional minimum length.
	 * Whenever PHP starts supporting method overloading, the variables will be reversed in
	 * order to make more logical sense.
	 * 
	 * @param int $limit1
	 * @param int $limit2
	 * @param string $userMessage
	 */
	public function __construct($limit1, $limit2 = NULL, $userMessage = NULL)
	{
		$this->min = (int)$limit1;
		if(isset($limit2))
		{
			if($limit2 >= $limit1)
			{
				$this->max = (int)$limit2;
			}
			else 
			{
				$this->min = (int)$limit2;
				$this->max = (int)$limit1;
			}
		}
		parent::__construct($userMessage);
	}
	
	/**
	 * @return int $min
	 */
	public function getMin(): int
	{
		return $this->min;
	}

	/**
	 * @return int $max
	 */
	public function getMax(): int
	{
		return $this->max;
	}

	/**
	 * @param int $min
	 * @return $this
	 */
	public function setMin(int $min): LengthValidator
	{
		$this->min = $min;
		return $this;
	}

	/**
	 * @param int $max
	 * @return $this
	 */
	public function setMax(int $max): LengthValidator
	{
		$this->max = $max;
		return $this;
	}

	/**
	 * Check for Data Length Validity.
	 * @param mixed $data
	 * @return boolean
	 */
	public function check($data): bool
	{
		$data = (string)$data;
		if(strlen($data) >= $this->min)
		{
			if(isset($this->max) && strlen($data) <= $this->max)
			{
				return true;
			}
			elseif(!isset($this->max))
			{
				return true;
			}
			else 
			{
				$this->addError($this->userMessage ?? self::MAX_LENGTH_ERROR);
			}
		}
		else
		{
			$this->addError($this->userMessage ?? self::MIN_LENGTH_ERROR);
		}

		return false;
	}
}