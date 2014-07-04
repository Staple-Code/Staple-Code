<?php
/**
 * Text element for use on forms.
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

class Staple_Form_TextElement extends Staple_Form_Element
{
	/**
	 * Size of the text field.
	 * @var int
	 */
	protected $size;
	/**
	 * Maxlength of the textfield.
	 * @var int
	 */
	protected $max;
	
	/**
	 * @return the $size
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return the $max
	 */
	public function getMax()
	{
		return $this->max;
	}

	/**
	 * @param int $size
	 */
	public function setSize($size)
	{
		$this->size = (int)$size;
		return $this;
	}

	/**
	 * @param int $max
	 */
	public function setMax($max)
	{
		$this->max = (int)$max;
		return $this;
	}

	/**
	 * Build the field label.
	 * @see Staple_Form_Element::label()
	 * @return string
	 */
	public function label()
	{
		return '	<label for="'.$this->escape($this->id).'"'.$this->getClassString().'>'.$this->label."</label>\n";
	}

	/**
	 * Build the field itself.
	 * @see Staple_Form_Element::field()
	 * @return string
	 */
	public function field()
	{
		$size = '';
		$max = '';
		if(isset($this->size))
		{
			$size = ' size="'.((int)$this->size).'"';
		}
		if(isset($this->max))
		{
			$max = ' maxlength="'.((int)$this->max).'"';
		}
		return '	<input type="text" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'"'.$size.$max.$this->getAttribString().'>'."\n";
	}

	/**
	 * Build the form field.
	 * @see Staple_Form_Element::build()
	 * @return string
	 */
	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/TextElement.phtml';
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
			$this->addClass('element_text');
			$classes = $this->getClassString();
			$buf .= "<div$classes id=\"".$this->escape($this->id)."_element\">\n";
			$buf .= $this->label();
			$buf .= $this->field();
			$buf .= $this->instructions();
			$buf .= "</div>\n";
		}
		return $buf;
	}
}