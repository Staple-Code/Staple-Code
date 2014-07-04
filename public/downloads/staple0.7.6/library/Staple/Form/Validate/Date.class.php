<?php
/** 
 * @author scott
 * 
 * 
 */
class Staple_Form_Validate_Date extends Staple_Form_Validator
{
	const REGEX = '/^(((0?[13578]|1[02])[\- \/\.](0?[1-9]|[12][0-9]|3[01]))|(0?2[\- \/\.](0?[1-9]|[12][0-9]))|((0?[469]|11)[\- \/\.](0?[1-9]|[12][0-9]|3[0])))[\- \/\.]((19|20)\d\d)$/';
	
	const DEFAULT_ERROR = 'Field must be a valid date.';

	/**
	 * @param  mixed $data
	 * @return  bool
	 * @see Staple_Form_Validator::check()
	 */
	public function check($data)
	{
		if(preg_match(self::REGEX, $data, $this->matches))
		{
			return true;
		}
		else
		{
			$this->addError();
			return false;
		}
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
		
		$script = "\t//Date Validator for ".addslashes($field->getLabel())."\n";
		$script .= "\tif(!(".self::REGEX.".test($('$valstring').val())))\n\t{\n";
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