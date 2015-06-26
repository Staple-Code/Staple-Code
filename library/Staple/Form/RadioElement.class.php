<?php
/**
 * A class to create radio button element groups.
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

use \Staple\Error;
use \Exception;

class RadioElement extends FieldElement
{
	const SORT_VALUES = 1;
	const SORT_LABELS_ALPHA = 2;
	const SORT_LABELS_REVERSE = 3;
	const SORT_USER = 4;
	
	/**
	 * An array that holds the options list for the select box. The keys represent the values of the options,
	 * and the values of the array are the labels for the options.
	 * @var array
	 */
	protected $buttons = array();
	/**
	 * Boolean whether or not any button has been checked. Corrects for a "0" value radio box.
	 * @var boolean
	 */
	protected $checked = false;
	
	/**
	 * Add a single option to the select list.
	 * 
	 * @param mixed $value
	 * @param string $label
	 * @throws Exception
	 */
	public function addButton($value,$label = NULL)
	{
		if(is_array($value) || is_resource($value))
		{
			throw new Exception('Select values must be strings or integers.', Error::APPLICATION_ERROR);
		}
		else 
		{
			if(isset($label))
			{
				$this->buttons[$value] = $label;
			}
			else
			{
				$this->buttons[$value] = $value;
			}
		}
		return $this;
	}
	
	/**
	 * Add an array of values to the select list. Keys of the array become values of the options and the values
	 * become the labels for the options. The second option allows the use of the labels as the values for the
	 * options.
	 * 
	 * @param array $options
	 * @param boolean $labelvalues
	 * @throws Exception
	 */
	public function addButtonsArray(array $options, $labelvalues = FALSE)
	{
		foreach($options as $value=>$label)
		{
			if(is_array($value) || is_resource($value))
			{
				throw new Exception('Select values must be strings or integers.', Error::APPLICATION_ERROR);
			}
			else
			{
				if($labelvalues === true)
				{
					$this->buttons[$label] = $label;
				}
				else 
				{
					$this->buttons[$value] = $label;
				}
			} 
		}
		return $this;
	}
	
	/**
	 * Returns the options array.
	 * @return array
	 */
	public function getButtons()
	{
		return $this->buttons;
	}
	
	/**
	 * Removes a button by its value.
	 * @param mixed $value
	 * @return $this
	 */
	public function removeButtonByValue($value)
	{
		unset($this->buttons[$value]);
		return $this;
	}
	
	/**
	 * Removes a button by searching for the button name.
	 * @param string $name
	 * @return boolean
	 */
	public function removeButtonByName($name)
	{
		if(($key = array_search($name, $this->buttons)) !== false)
		{
			unset($this->buttons[$key]);
			return true;
		}
		return false;
	}
	
	/**
	 * Sorts the options list based on a set of preset sorts.
	 * @param int $how
	 * @param callback $sortFunction
	 */
	public function sortOptions($how, $sortFunction = NULL)
	{
		switch($how)
		{
			case self::SORT_VALUES :
				ksort($this->buttons);
				break;
			case self::SORT_LABELS_ALPHA :
				asort($this->buttons);
				break;
			case self::SORT_LABELS_REVERSE :
				arsort($this->buttons);
				break;
			case self::SORT_USER:
				usort($this->buttons, $sortFunction);
				break;
				
		}
		return $this;
	}
	
	public function setValue($insert)
	{
		$this->checked = true;
		return parent::setValue($insert);
	}
	
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		$buf = '';
		foreach($this->buttons as $value=>$label)
		{
			$check = '';
			if($this->value == $value && $this->checked === true)
			{
				$check = ' checked';
			}
			$buf .= "	<div class=\"form_radio\" id=\"".$this->escape($this->id)."_".$this->escape($value)."_div\">\n";
			$buf .= "		<input type=\"radio\" name=\"".$this->escape($this->name)."\" id=\"".$this->escape($this->id)."_".$this->escape($value)."\" value=\"".$this->escape($value)."\"$check".$this->getAttribString('input').">\n";
			$buf .= "		<label for=\"".$this->escape($this->id)."_".$this->escape($value)."\">".$this->escape($label)."</label>\n";
			$buf .= "	</div>\n";
		}
		return $buf;
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return '	<label'.$this->getClassString('label').'>'.$this->label."</label>\n";
	}

	/**
	 * Builds the select list form element.
	 * 
	 * @see Staple_Form_Element::build()
	 */
	public function build($fieldView = NULL)
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/RadioGroup.phtml';
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
			$this->addClass('element_radiogroup');
			$classes = $this->getClassString('div');
			$buf .= "<div$classes id=\"".$this->escape($this->id)."_element\">\n";
			$buf .= $this->label();
			$buf .= $this->field();
			$buf .= $this->instructions();
			$buf .= "</div>\n";
		}
		return $buf;
	}
}