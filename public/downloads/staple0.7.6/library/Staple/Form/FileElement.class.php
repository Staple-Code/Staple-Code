<?php
/** 
 * Creates a file field to be added to the form. Class is incomplete.
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
class Staple_Form_FileElement extends Staple_Form_Element
{
	/**
	 * Holds the MIME for the HTML Accept property
	 * @var string
	 */
	protected $accept;
	
	/**
	 * @return the $accept
	 */
	public function getAccept()
	{
		return $this->accept;
	}

	/**
	 * @param string $accept
	 * @return Staple_Form_FileElement
	 */
	public function setAccept($accept)
	{
		$this->accept = $accept;
		return $this;
	}

	/**
	 * 
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		$accept = '';
		if(isset($this->accept))
		{
			$accept = ' accept="'.htmlentities($this->accept).'"';
		}
		return '	<input type="file" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'"'.$accept.$this->getAttribString().'>'."\n";
	}

	/**
	 * 
	 * @see Staple_Form_Element::instructions()
	 */
	public function instructions()
	{
		if(strlen($this->instructions) > 0)
		{
			return '	<p class="field_instructions">'.$this->escape($this->instructions).'</p>';
		}
		else
		{
			return '';
		}
	}

	/**
	 * 
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return '	<label for="'.$this->escape($this->id).'"'.$this->getClassString().'>'.$this->label."</label>\n";
	}

	/**
	 * 
	 * @see Staple_Form_Element::build()
	 */
	public function build()
	{
		$buf = '';
		$this->addClass('form_element');
		$this->addClass('element_file');
		$classes = $this->getClassString();
		$buf .= "<div$classes id=\"".$this->escape($this->id)."_element\">\n";
		$buf .= $this->label();
		$buf .= $this->field();
		$buf .= $this->instructions();
		$buf .= "</div>\n";
		return $buf;
	}
}

?>