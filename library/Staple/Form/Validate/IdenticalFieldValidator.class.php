<?php
/** 
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
namespace Staple\Form\Validate;

use \Staple\Form\FieldValidator;
use \Staple\Form\FieldElement;

class IdenticalFieldValidator extends FieldValidator
{
	const DEFAULT_ERROR = 'Data is not equal';
	
	protected $strict = false;
	/**
	 * The form element to validate against.
	 * @var Staple_Form_Element
	 */
	protected $field;
	
	public function __construct(FieldElement $field = NULL, $strict = false, $usermsg = NULL)
	{
		if(isset($field))
		{
			$this->setField($field);
		}
		$this->strict = (bool)$strict;
		parent::__construct($usermsg);
	}

	/**
	 * 
	 * @param  mixed $data
 
	 * @return  bool
	  
	 * @see Staple_Form_Validator::check()
	 */
	public function check($data)
	{
		if($this->strict === true)
		{
			if($this->field->getValue() === $data)
			{
				return true;
			}
			else
			{
				$this->addError();
			}
		}
		else
		{
			if($this->field->getValue() == $data)
			{
				return true;
			}
			else
			{
				$this->addError();
			}
		}
		return false;
	}
	
	/**
	 * @return the $strict
	 */
	public function getStrict()
	{
		return $this->strict;
	}

	/**
	 * @return Staple_Form_Element $field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @param boolean $strict
	 */
	public function setStrict($strict)
	{
		$this->strict = (bool)$strict;
		return $this;
	}

	/**
	 * @param Staple_Form_Element $field
	 */
	public function setField(FieldElement $field)
	{
		$this->field = $field;
		return $this;
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
		
		switch (get_class($this->field))
		{
			case 'Staple_Form_SelectElement':
				$identstring = "#{$field->getId()} option:selected";
				break;
			case 'Staple_Form_RadioGroup':
				$identstring = "input:radio[name={$field->getName()}]:checked";
				break;
			case 'Staple_Form_CheckboxElement':
				return '';
				break;
			default:
				$identstring = $fieldid;
		}
		
		$script = "\t//Identical Validator for ".addslashes($field->getLabel())."\n";
		$script .= "\tif(!($('$valstring').val() == $('$identstring').val()))\n\t{\n";
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