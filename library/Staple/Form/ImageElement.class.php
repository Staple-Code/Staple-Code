<?php
/**
 * Image button element for use on forms.
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

use Staple\Dev;

class ImageElement extends FieldElement
{
	protected $src;
	
	public function setSrc($insert)
	{
		$this->src = $insert;
		return $this;
	}
	
	public function getSrc()
	{
		return $this->src;
	}
	
	/* (non-PHPdoc)
	 * @see Staple_Form_Element::field()
	 */
	public function field()
	{
		if(array_key_exists('host', parse_url($this->src)))
		{
			$imgSrc = $this->src;
		}
		else
		{
			$imgSrc = $this->link($this->src);
		}

		return '	<input type="image" src="'. $imgSrc .'" id="'.$this->escape($this->id).'" name="'.$this->escape($this->name).'" value="'.$this->escape($this->value).'">';
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Element::label()
	 */
	public function label()
	{
		return '	<label for="'.$this->escape($this->id).'"'.$this->getClassString('label').'>'.$this->label.'</label>';
	}

	public function build($fieldView = NULL)
	{
		$buf = '';
		$view = FORMS_ROOT.'/fields/ImageElement.phtml';
		if(file_exists($view))
		{
			ob_start();
			include $view;
			$buf = ob_get_contents();
			ob_end_clean();
		}
	        elseif(isset($this->elementViewAdapter))
	        {
	            $buf = $this->getElementViewAdapter()->ImageElement($this);
	        }
		else 
		{
			$this->addClass('form_element');
			$this->addClass('element_image');
			$classes = $this->getClassString('div');
			$buf .= "<div$classes id=\"".$this->escape($this->id)."_element\">\n";
			if(isset($this->label))
			{
				$buf .= $this->label();
			}
			$buf .= $this->field();
			$buf .= '</div>';
		}
		return $buf;
	}
}