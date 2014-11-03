<?php
/**
 * Checkbox element for use on forms.
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
namespace Staple\Form;

class CheckboxElement extends FieldElement
{
	private $changed = false;
	
	private $checked = false;
	/**
	 * 
	 * Override the default field value
	 * @var unknown_type
	 */
	protected $value = 1;
	
	public function setChecked($bool = true)
	{
		$bool = (bool)$bool;
		$this->checked = $bool;
		return $this;
	}
	
	/**
	 * Returns a boolean whether the checkbox is checked or not.
	 * @return boolean
	 */
	public function isChecked()
	{
		if($this->checked === true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Sets the starting value for the checkbox
	 * @param boolean $val
	 * @return Staple_Form_CheckboxElement
	 */
	public function setValue($val)
	{
		$this->setChecked($val);
		$this->changed = true;
		return $this;
	}
	
	/**
	 * Returns the starting value for the checkbox
	 * @return boolean
	 */
	public function getValue()
	{
		return (bool)$this->isChecked();
	}
	
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		$checked = '';
		if($this->isChecked())
		{
			$checked = ' checked';
		}
		return '	<input type="checkbox" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'"'.$checked.$this->getAttribString().'>';
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return '	<label for="'.$this->escape($this->id).'"'.$this->getClassString().'>'.$this->label.'</label>';
	}

	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/CheckboxElement.phtml';
		if(file_exists($view))
		{
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$this->addClass('form_element');
			$this->addClass('element_checkbox');
			$classes = $this->getClassString();
			$buf .= "<div$classes id=\"".$this->escape($this->id)."_element\">\n";
			$buf .= $this->field();
			$buf .= $this->label();
			$buf .= '</div>';
		}
		return $buf;
	}
}