<?php
/** 
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

class CheckboxGroupElement extends FieldElement
{
	/**
	 * An array that holds the Checkbox elements.
	 * @var CheckboxGroupElement[]
	 */
	protected $boxes = array();
	
	public function addCheckbox(CheckboxElement $box)
	{
		$this->boxes[] = $box;
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
			if($box instanceof CheckboxElement)
			{
				$this->addCheckbox($box);
			}
		}
		return $this;
	}
	
	/**
	 * Return all the checkbox objects
	 * @return array[Staple_Form_CheckboxElement]
	 */
	public function getBoxes()
	{
		return $this->boxes;
	}
	
	/**
	 * Sorts the boxes by Label
	 */
	public function sortBoxesByLabel()
	{
		usort($this->boxes, array($this, 'sortCmpLabel'));
		return $this;
	}
	
	/**
	 * Sorts the boxes by Name
	 */
	public function sortBoxesByName()
	{
		usort($this->boxes, array($this, 'sortCmpName'));
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
	 * Returns an associative array of 
	 * @return array
	 */
	public function getValue()
	{
		$values = array();
		foreach($this->boxes as $key=>$value)
		{
			$values[$value->getName()] = $value->getValue();
		}
		return $values;
	}
	
	/**
	 * This function requires an associative array composed of the form field names, followed by their values.
	 * @return $this
	 */
	public function setValue(array $inserts)
	{
		foreach($this->boxes as $key=>$value)
		{
			if(array_key_exists($value->getName(), $inserts))
			{
				$this->boxes[$key]->setValue($inserts[$value->getName()]);
			}
		}
		return $this;
	}
	
	/**
	 * Comparison function for sorting by names
	 * @param $this $a
	 * @param $this $b
	 */
	private function sortCmpName(CheckboxElement $a, CheckboxElement $b)
	{
		return strcmp($a->getName(), $b->getName());
	}
	
	/**
	 * Comparison function for sorting by labels
	 * @param $this $a
	 * @param $this $b
	 */
	private function sortCmpLabel(CheckboxElement $a, CheckboxElement $b)
	{
		return strcmp($a->getLabel(), $b->getLabel());
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
		return $buff;
	}

	/**
	 * 
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return "<label".$this->getClassString('label').">".$this->escape($this->getLabel())."</label>";
	}

	/**
	 * 
	 * @see Staple_Form_Element::build()
	 */
	public function build($fieldView = NULL)
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
        elseif(isset($this->viewAdapter))
        {
            $this->viewAdapter->CheckboxGroupElement($this);
        }
		else 
		{
			$this->addClass('form_element');
			$this->addClass('element_checkboxgroup');
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
}

?>