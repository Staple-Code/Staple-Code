<?php
/** 
 * Validates the length of a form field.
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
class Staple_Form_Validate_Length extends Staple_Form_Validator
{
	const DEFAULT_ERROR = 'Field does not meet length requirements.';
	protected $min = 0;
	protected $max;
	
	/**
	 * Accepts a maximum length to validate against. Also accepts an optional minimum length.
	 * Whenever PHP starts supporting method overloading, the variables will be reversed in
	 * order to make more logical sense.
	 * 
	 * @param int $max
	 * @param int $min
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
	 * @return the $min
	 */
	public function getMin()
	{
		return $this->min;
	}

	/**
	 * @return the $max
	 */
	public function getMax()
	{
		return $this->max;
	}

	/**
	 * @param int $min
	 */
	public function setMin($min)
	{
		$this->min = $min;
		return $this;
	}

	/**
	 * @param int $max
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
		if(strlen($data) <= $this->max)
		{
			if(strlen($data) >= $this->min)
			{
				return true;
			}
			else 
			{
				$this->addError("Minimum Length Not Met");
			}
		}
		else
		{
			$this->addError("Maximum Length Exceeded");
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Staple_Form_Validator::clientJQuery()
	 */
	public function clientJQuery($fieldType, Staple_Form_Element $field)
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
		
		$script = "\t//Length Validator for ".$field->getLabel()."\n";
		$script .= "\tif($('$valstring').val().length > {$this->getMax()} || $('$valstring').val().length < {$this->getMin()})\n";
		$script .= "\t{\n";
		$script .= "\t\terrors.push('{$field->getLabel()}: \\n{$this->clientJSError()}\\n');\n";
		$script .= "\t\t$('$fieldid').addClass('form_error');\n";
		$script .= "\t}\n";
		$script .= "\telse {\n";
		$script .= "\t\t$('$fieldid').removeClass('form_error');\n";
		$script .= "\t}\n";
		return $script;
	}
}

?>