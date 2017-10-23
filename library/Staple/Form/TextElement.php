<?php
/**
 * Text element for use on forms.
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

use Staple;

class TextElement extends FieldElement
{
	/**
 	* HTML5 input type
 	* @var string
 	*/
	protected $type;

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
	 * @return string $type
 	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return int $size
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return int $max
	 */
	public function getMax()
	{
		return $this->max;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @param int $size
	 * @return $this
	 */
	public function setSize($size)
	{
		$this->size = (int)$size;
		return $this;
	}

	/**
	 * @param int $max
	 * @return $this
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
		return '	<label for="'.$this->escape($this->id).'"'.$this->getClassString('label').'>'.$this->label."</label>\n";
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

		$type = isset($this->type) ? $this->type : 'text';

		if(isset($this->size))
		{
			$size = ' size="'.((int)$this->size).'"';
		}
		if(isset($this->max))
		{
			$max = ' maxlength="'.((int)$this->max).'"';
		}
		return '	<input type="'.$type.'" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'"'.$size.$max.$this->getAttribString('input').'>'."\n";
	}


	/**
	 * Return the built form element
	 * @return string
	 */
	public function build()
	{
		$buf = '';
		//@todo look into folder structure for field views
		$view = FORMS_ROOT.'/fields/TextElement.phtml';
		if(file_exists($view))
		{
			//@todo use the custom view
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
		elseif(isset($this->elementViewAdapter))
		{
			$buf = $this->getElementViewAdapter()->TextElement($this);
		}
		else
		{
			$this->addClass('form_element');
			$this->addClass('element_text');
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
