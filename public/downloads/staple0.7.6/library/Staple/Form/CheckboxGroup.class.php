<?php

require_once ('library/Staple/Form/Element.class.php');

/** 
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
class Staple_Form_CheckboxGroup extends Staple_Form_Element
{
	/**
	 * An array that holds the Checkbox elements.
	 * @var array[Staple_Form_CheckboxElement]
	 */
	protected $boxes = array();
	
	public function addCheckbox(Staple_Form_CheckboxElement $box)
	{
		$this->boxes[$box->getName()] = $box;
		return $this;
	}
	
	/**
	 * Adds multiple checkboxes to the array of checkboxes; 
	 * @param array $boxes
	 */
	public function addCheckboxArray(array $boxes)
	{
		foreach($boxes as $box)
		{
			if($box instanceof Staple_Form_CheckboxElement)
			{
				$this->addCheckbox($box);
			}
		}
		return $this;
	}
	
	public function sortBoxesByName()
	{
		ksort($this->boxes);
		return $this;
	}
	
	public function sortBoxesByLabel()
	{
		usort($this->boxes, array($this, 'sortcmp'));
		return $this;
	}
	
	/**
	 * This simple function resets the $boxes array.
	 */
	public function clearBoxes()
	{
		$this->boxes = array();
		return $this;
	}
	
	/**
	 * Comparison function for sorting by labels
	 * @param Staple_Form_CheckboxElement $a
	 * @param Staple_Form_CheckboxElement $b
	 */
	private function sortcmp(Staple_Form_CheckboxElement $a, Staple_Form_CheckboxElement $b)
	{
		return strcmp($a->getName(), $b->getName());
	}
	
	//-------------------------------------------------BUILDERS-------------------------------------------------

	/**
	 * 
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		$buff = '<div class="form_checkboxes">';
		foreach ($this->boxes as $box)
		{
			$buff .= $box->build();
		}
		$buff .= '</div>';
	}

	/**
	 * 
	 * @see Staple_Form_Element::instructions()
	 */
	public function instructions()
	{
	
	}

	/**
	 * 
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return "<label".$this->getClassString().">".$this->escape($this->getLabel())."</label>";
	}

	/**
	 * 
	 * @see Staple_Form_Element::build()
	 */
	public function build()
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/CheckboxGroup.phtml';
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
			$this->addClass('element_checkboxgroup');
			$classes = $this->getClassString();
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
}

?>