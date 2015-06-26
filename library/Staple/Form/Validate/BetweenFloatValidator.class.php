<?php
/** 
 * Validates a numeric value is between the min and the max.
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

use \Staple\Form\FieldValidator;
use \Staple\Form\FieldElement;

class BetweenFloatValidator extends FieldValidator
{
	const DEFAULT_ERROR = 'Field is not between minimum and maximum values.';
	/**
	 * Minimum Value
	 * @var float
	 */
	protected $min = 0;
	/**
	 * Maximum Value
	 * @var float
	 */
	protected $max;
	
	/**
	 * Mathematical between function. Requires a maximum value and a minimum value.
	 * Comparison occurs with float math.
	 * 
	 * @param float $max
	 * @param float $min
	 */
	public function __construct($limit1, $limit2, $usermsg = NULL)
	{
		$this->min = (float)$limit1;
		if(isset($limit2))
		{
			if($limit2 >= $limit1)
			{
				$this->max = (float)$limit2;
			}
			else 
			{
				$this->min = (float)$limit2;
				$this->max = (float)$limit1;
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
		$data = (float)$data;
		if($data <= ($this->max+0.0625) && $data >= $this->min)			//+0.06256 Binary Float fix
		{
			return true;
		}
		else
		{
			$this->addError();
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Staple_Form_Validator::clientJQuery()
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
		
		$script = "\t//BetweenFloat Validator for ".addslashes($field->getLabel())."\n";
		$script .= "\tif($('$valstring').val() > {$this->getMax()} || $('$valstring').val() < {$this->getMin()})\n";
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

?>