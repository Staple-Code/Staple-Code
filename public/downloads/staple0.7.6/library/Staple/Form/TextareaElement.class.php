<?php
/**
 * Textarea element for use on forms.
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
class Staple_Form_TextareaElement extends Staple_Form_Element
{
	protected $rows;
	protected $cols;
	
	public function setRows($rows)
	{
		$this->rows = (int)$rows;
		return $this;
	}
	public function getRows()
	{
		return $this->rows;
	}
	public function setCols($cols)
	{
		$this->cols = (int)$cols;
		return $this;
	}
	public function getCols()
	{
		return $this->cols;
	}
	
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		return '	<textarea rows="'.$this->rows.'" cols="'.$this->cols.'" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'"'.$this->getAttribString().'>'.$this->escape($this->value)."</textarea>\n";
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::instructions()
	 */
	public function instructions()
	{
		return '<p class="field_instructions">'.$this->instructions.'</p>';
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return '	<label for="'.$this->escape($this->id).'"'.$this->getClassString().'>'.$this->label."</label>\n";
	}

	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/TextareaElement.phtml';
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
			$this->addClass('element_textarea');
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