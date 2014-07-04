<?php
/** 
 * @author scott
 * 
 * 
 */
class Staple_Form_Validate_Numeric extends Staple_Form_Validator
{
	const DEFAULT_ERROR = 'Field value must be numeric.';

	/**
	 * 
	 * @param  mixed $data
 
	 * @return  bool
	  
	 * @see Staple_Form_Validator::check()
	 */
	public function check($data)
	{
		if(ctype_digit($data) === true)
		{
			return true;
		}
		else
		{
			$this->addError(self::DEFAULT_ERROR);
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
		
		$script = "\t//Numeric Validator for ".addslashes($field->getLabel())."\n";
		$script .= "\tif(!(/^[0-9]+$/.test($('$valstring').val())))\n";
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