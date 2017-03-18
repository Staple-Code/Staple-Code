<?php
/**
 * Submit button element for use on forms.
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

use Staple\Exception\FormBuildException;

class ButtonElement extends FieldElement
{
	public function __construct($name, $value = NULL, $id = NULL, array $attrib = array(), $label = NULL)
	{
		parent::__construct($name,$label,$id,$attrib);
		if(isset($value))
		{
			$this->value = $value;
		}
	}
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		return '	<input type="button" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'"'.$this->getAttribString('input').">\n";
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return "	<label for=\"".$this->escape($this->id)."\"".$this->getClassString('label').">".$this->label."</label>\n";
	}

	/**
	 * Return the built form element
	 * @return string
	 */
	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/SubmitElement.phtml';
		if(file_exists($view))
		{
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
		elseif(isset($this->elementViewAdapter))
		{
			$buf = $this->getElementViewAdapter()->ButtonElement($this);
		}
		else 
		{
			$this->addClass('form_element');
			$this->addClass('element_button');
			$classes = $this->getClassString('div');
			$buf .= "<div$classes id=\"".$this->escape($this->id)."_element\">\n";
			if(isset($this->label))
			{
				$buf .= $this->label(); 
			}
			$buf .= $this->field();
			$buf .= "</div>\n";
		}
		return $buf;
	}

	/**
	 * Throws an exception because validators are not allowed on this element.
	 * @param FieldValidator $validator
	 * @return $this
	 * @throws FormBuildException
	 */
	public function addValidator(FieldValidator $validator)
	{
		throw new FormBuildException('Submit elements do not have validators.');
	}
}