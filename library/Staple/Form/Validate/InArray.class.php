<?php
/** 
 * Validates that the supplied value is within an array of valid values.
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
namespace Staple\Form\Validate;

use \Staple\Form\FieldValidator;
use \Staple\Form\FieldElement;

class InArray extends FieldValidator
{
	const DEFAULT_ERROR = 'Supplied data not in accepted list of values.';
	/**
	 * Valid array values.
	 * @var array
	 */
	protected $arrayvalues = array();

	/**
	 * Supply an array to the constructor to define valid options.
	 * @param array $values
	 */
	function __construct(array $values = array(), $usermsg = NULL)
	{
		$this->arrayvalues = $values;
		parent::__construct($usermsg);
	}
	
	/**
	 * Add a new value to the valid array list.
	 * @param mixed $value
	 */
	public function addValue($value)
	{
		if(is_array($value))
		{
			$this->arrayvalues = array_merge($this->arrayvalues,$value);
		}
		else
		{
			$this->arrayvalues[] = $value;
		}
	}

	/**
	 * Check that the supplied data exists as a value in the array;
	 * @param mixed $data
	 * @return bool
	 * @see Staple_Form_Validator::check()
	 */
	public function check($data)
	{
		if(in_array($data, $this->arrayvalues) === true)
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
		
		$script = "\t//Selection Validator for ".addslashes($field->getLabel())."\n";
		$script .= "\tif(-1 == $.inArray($('$valstring').val(),[";
		foreach($this->arrayvalues as $value)
		{
			$script .= "'$value',";
		}
		$script = substr($script, 0,strlen($script)-1);
		$script .= "]))\n\t{\n";
		
		$script .= "\t\terrors.push('".addslashes($field->getLabel()).": \\n{$this->clientJSError()}\\n');\n";
		$script .= "\t\t$('$fieldid').addClass('form_error');\n";
		$script .= "\t}\n";
		$script .= "\telse {\n";
		$script .= "\t\t$('$fieldid').removeClass('form_error');\n";
		$script .= "\t}\n";
		
		return $script;
	}

	/**
	 * @see Staple_Form_Validator::clientJS()
	 */
	public function clientJS($fieldType, FieldElement $field)
	{
		// TODO Auto-generated method stub
		
	}

}

?>