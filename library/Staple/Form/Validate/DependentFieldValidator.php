<?php
/**
 *
 * @author Ironpilot
 * @Updated Hans Heeling
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
namespace Staple\Form\Validate;

use Staple\Form\IFieldElement;

class DependentFieldValidator extends BaseFieldValidator
{
	const DEFAULT_ERROR = 'Field Comparison Error';

	/**
	 * Constants for comparison types
	 */
	const EQUAL = 1;
	const LESSTHAN = 2;
	const GREATERTHAN = 3;
	const LESSTHANEQUALTO = 4;
	const GREATERTHANEQUALTO = 5;

	/**
	 * @var
	 *
	 * Check operation to perform
	 */
	protected $operation = self::EQUAL;

	/**
	 * @param IFieldElement $field
	 * @param $operation
	 * @param string $userMsg
	 */
	public function __construct(IFieldElement $field, $operation = self::EQUAL, $userMsg = NULL)
	{
		$this->field = $field;
		$this->operation = $operation;

		parent::__construct($userMsg);
	}

    /**
     * @return string
     */
	public function getFieldValue(): string
	{
		return $this->field->getValue();
	}

	/**
	 * @return mixed
	 */
	public function getOperation()
	{
		return $this->operation;
	}

	/**
	 * @param $operation
	 * @return $this
	 */
	public function setOperation($operation): DependentFieldValidator
	{
		$this->operation = $operation;
		return $this;
	}

	/*
	 * Static functions for creating validation dependency
	 */

	/**
	 * @param IFieldElement $field
	 * @param string|null $userMsg
	 * @return DependentFieldValidator
	 *
	 * Instantiates DependentFieldValidator for an equal to comparison between fields
	 */
	public static function equal(IFieldElement $field, $userMsg = NULL): DependentFieldValidator
	{
		return new self($field, self::EQUAL, $userMsg);
	}

	/**
	 * @param IFieldElement $field
	 * @param string|null $userMsg
	 * @return DependentFieldValidator
	 *
	 * Instantiates DependentFieldValidator for a less than comparison between two fields. Compares strings by string length.
	 */
	public static function lessThan(IFieldElement $field, $userMsg = NULL)
	{
		return new self($field, self::LESSTHAN, $userMsg);
	}

	/**
	 * @param IFieldElement $field
	 * @param string|null $userMsg
	 * @return DependentFieldValidator
	 *
	 * Instantiates DependentFieldValidator for a greater than comparison between two fields. Compares strings by string length.
	 */
	public static function greaterThan(IFieldElement $field, $userMsg = NULL)
	{
		return new self($field, self::GREATERTHAN, $userMsg);
	}

	/**
	 * @param IFieldElement $field
	 * @param string|null $userMessage
	 * @return DependentFieldValidator
	 *
	 * Instantiates DependentFieldValidator for a less than or equal to comparison between two fields. Compares strings by string length.
	 */
	public static function lessThanEqualTo(IFieldElement $field, $userMessage = NULL): DependentFieldValidator
	{
		return new self($field, self::LESSTHANEQUALTO, $userMessage);
	}

	/**
	 * @param IFieldElement $field
	 * @param string|null $userMsg
	 * @return DependentFieldValidator
	 *
	 * Instantiates DependentFieldValidator for a greater than or equal to comparison between two fields. Compares strings by string length.
	 */
	public static function greaterThanEqualTo(IFieldElement $field, $userMsg = NULL): DependentFieldValidator
	{
		return new self($field, self::GREATERTHANEQUALTO, $userMsg);
	}
	
	/**
	 * 
	 * @param  mixed $data
	 * @return  bool
	 *
	 * Method to perform actual data check
	 */
	public function check($data): bool
	{
		switch ($this->getOperation())
		{
			case self::EQUAL:
				return $this->equalComparison($data);
				break;

			case self::LESSTHAN:
				return $this->lessThanComparison($data);
				break;

			case self::GREATERTHAN:
				return $this->greaterThanComparison($data);
				break;

			case self::LESSTHANEQUALTO:
				return $this->lessThanEqualToComparison($data);
				break;

			case self::GREATERTHANEQUALTO:
				return $this->greaterThanEqualToComparison($data);
				break;
		}
		$this->addError();
		return false;
	}

	/**
	 * Comparison Functions
	 */

	/**
	 * @param $data
	 * @return bool
	 *
	 * Equal to comparison check
	 */
	private function equalComparison($data): bool
	{
		if($data == $this->getFieldValue())
		{
			return TRUE;
		}
		$this->addError();
		return FALSE;
	}

	/**
	 * @param $data
	 * @return bool
	 *
	 * Less than comparison check
	 */
	private function lessThanComparison($data): bool
	{
		if(is_numeric($data))
		{
			if($data < $this->getFieldValue())
			{
				return TRUE;
			}
		}
		else
		{
			if(strlen($data) < strlen($this->getFieldValue()))
			{
				return TRUE;
			}
		}
		$this->addError();
		return FALSE;
	}

	/**
	 * @param $data
	 * @return bool
	 *
	 * Greater than comparison check
	 */
	private function greaterThanComparison($data): bool
	{
		if(is_numeric($data))
		{
			if($data > $this->getFieldValue())
			{
				return TRUE;
			}
		}
		else
		{
			if(strlen($data) > strlen($this->getFieldValue()))
			{
				return TRUE;
			}
		}
		$this->addError();
		return FALSE;
	}

	/**
	 * @param $data
	 * @return bool
	 *
	 * Less than or Equal to comparison check
	 */
	private function lessThanEqualToComparison($data): bool
	{
		if(is_numeric($data))
		{
			if($data <= $this->getFieldValue())
			{
				return TRUE;
			}
		}
		else
		{
			if(strlen($data) <= strlen($this->getFieldValue()))
			{
				return TRUE;
			}
		}
		$this->addError();
		return FALSE;
	}

	/**
	 * @param $data
	 * @return bool
	 *
	 * Greater than or Equal to comparison check
	 */
	private function greaterThanEqualToComparison($data): bool
	{
		if(is_numeric($data))
		{
			if($data >= $this->getFieldValue())
			{
				return TRUE;
			}
		}
		else
		{
			if(strlen($data) >= strlen($this->getFieldValue()))
			{
				return TRUE;
			}
		}
		$this->addError();
		return FALSE;
	}
}