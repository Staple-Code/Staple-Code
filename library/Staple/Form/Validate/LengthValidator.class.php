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
namespace Staple\Form\Validate;

use Staple\Form\FieldElement;
use Staple\Form\FieldValidator;

class LengthValidator extends FieldValidator
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
	 * @param string $usermsg
	 */
	public function __construct($limit1, $limit2 = NULL, $usermsg = NULL)
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
		parent::__construct($usermsg);
	}
	
	/**
	 * @return int $min
	 */
	public function getMin()
	{
		return $this->min;
	}

	/**
	 * @return int $max
	 */
	public function getMax()
	{
		return $this->max;
	}

	/**
	 * @param int $min
	 * @return $this
	 */
	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}

	/**
	 * @param int $max
	 * @return $this
	 */
	public function setMax($max)
	{
		$this->max = $max;
		return $this;
	}

	/**
	 * Check for Data Length Validity.
	 * @param mixed $data
	 * @return boolean
	 */
	public function check($data)
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
				$this->addError(self::MAX_LENGTH_ERROR);
			}
		}
		else
		{
			$this->addError(self::MIN_LENGTH_ERROR);
		}

		return false;
	}

	/**
	 * @param string $fieldType
	 * @param FieldElement $field
	 * @return string
	 */
	public function clientJQuery($fieldType, FieldElement $field)
	{
		switch ($fieldType)
		{
			case 'Staple_Form_SelectElement':
				$fieldid = "#{$field->getId()}";
				$valstring = "#{$field->getId()} option:selected";
				break;
			case 'Staple_Form_RadioGroup':
				$fieldid = "input:radio[name={$field->getName()}]";
				$valstring = "input:radio[name={$field->getName()}]:checked";
				break;
			case 'Staple_Form_CheckboxElement':
				return '';
				break;
			default:
				$fieldid = "#{$field->getId()}";
				$valstring = $fieldid;
		}
		
		$script = "\t//Length Validator for ".addslashes($field->getLabel())."\n";
		$script .= "\tif($('$valstring').val().length > {$this->getMax()} || $('$valstring').val().length < {$this->getMin()})\n";
		$script .= "\t{\n";
		$script .= "\t\terrors.push('".addslashes($field->getLabel()).": \\n{$this->clientJSError()}\\n');\n";
		$script .= "\t\t$('$fieldid').addClass('form_error');\n";
		$script .= "\t}\n";
		$script .= "\telse {\n";
		$script .= "\t\t$('$fieldid').removeClass('form_error');\n";
		$script .= "\t}\n";
		return $script;
	}
}