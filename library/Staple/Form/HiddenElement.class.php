<?php
/**
 * Hidden element for use on forms.
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
namespace Staple\Form;

class HiddenElement extends FieldElement
{
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::Create()
	 */
	public static function Create($name, $value = NULL, $id = NULL, array $attrib = array()) 
	{
		$result = parent::Create($name, NULL, $id, $attrib);
		if(isset($value))
		{
			$result->setValue($value);
		}
		return $result;
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::__construct()
	 */
	public function __construct($name, $value = NULL, $id = NULL, array $attrib = array())
	{
		if(isset($value))
		{
			$this->setValue($value);
		}
		parent::__construct($name,NULL,$id,$attrib);
	}
	
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		return '	<input type="hidden" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'">'."\n";
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::instructions()
	 */
	public function instructions()
	{
		return '';
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return '';
	}

	public function build($fieldView = NULL)
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/HiddenElement.phtml';
		if(file_exists($view))
		{
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
        elseif(isset($this->elementViewAdapter))
        {
            $this->getElementViewAdapter()->HiddenElement($this);
        }
		else
		{
			$buf = $this->field();
		}
		return $buf;
	}
}