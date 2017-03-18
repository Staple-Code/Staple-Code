<?php
/** 
 * Validate a Date field.
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

class DateValidator extends FieldValidator
{
	// American date string
	const REGEX_AMERICAN = '/^(((0?[13578]|1[02])[\- \/\.](0?[1-9]|[12][0-9]|3[01]))|(0?2[\- \/\.](0?[1-9]|[12][0-9]))|((0?[469]|11)[\- \/\.](0?[1-9]|[12][0-9]|3[0])))[\- \/\.]((19|20)\d\d)$/';
	// ISO date string
	const REGEX_ISO = '/^((19|20)\d\d)[\- \/\.](((0?[13578]|1[02])[\- \/\.](0?[1-9]|[12][0-9]|3[01]))|(0?2[\- \/\.](0?[1-9]|[12][0-9]))|((0?[469]|11)[\- \/\.](0?[1-9]|[12][0-9]|3[0])))$/';

	const DEFAULT_ERROR = 'Field must be a valid date.';

	/**
	 * Match list
	 * @var array
	 */
	private $matches;

	/**
	 * @return array
	 */
	public function getMatches()
	{
		return $this->matches;
	}

	/**
	 * @param array $matches
	 * @return $this
	 */
	protected function setMatches(array $matches)
	{
		$this->matches = $matches;
		return $this;
	}

	/**
	 * @param  mixed $data
	 * @return  bool
	 * @see FieldValidator::check()
	 */
	public function check($data)
	{
		if(preg_match(self::REGEX_AMERICAN, $data, $this->matches))
		{
			return true;
		}
		else
		{
			if(preg_match(self::REGEX_ISO, $data, $this->matches))
			{
				return true;
			}
			else
			{
				$this->addError();
				return false;
			}
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldValidator::clientJQuery()
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
		
		$script = "\t//Date Validator for ".addslashes($field->getLabel())."\n";
		$script .= "\tif(!(".self::REGEX_AMERICAN.".test($('$valstring').val())) && !(".self::REGEX_ISO.".test($('$valstring').val())))\n\t{\n";
		$script .= "\t\terrors.push('".addslashes($field->getLabel()).": \\n{$this->clientJSError()}\\n');\n";
		$script .= "\t\t$('$fieldid').addClass('form_error');\n";
		$script .= "\t}\n";
		$script .= "\telse {\n";
		$script .= "\t\t$('$fieldid').removeClass('form_error');\n";
		$script .= "\t}\n";
		
		return $script;
	}
}