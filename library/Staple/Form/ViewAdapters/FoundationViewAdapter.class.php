<?php
/**
 * An adapter to format the form elements.
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
 *
 */

namespace Staple\Form\ViewAdapters;

use Staple\Form\TextElement;
use Staple\Form\TextareaElement;
use Staple\Form\SubmitElement;
use Staple\Form\SelectElement;
use Staple\Form\RadioElement;
use Staple\Form\PasswordElement;
use Staple\Form\ImageElement;
use Staple\Form\HiddenElement;
use Staple\Form\FileElement;
use Staple\Form\CheckboxGroupElement;
use Staple\Form\CheckboxElement;
use Staple\Form\ButtonElement;

class FoundationViewAdapter extends ElementViewAdapter
{

	public function TextElement(TextElement $field)
	{
		//@todo add the class string in to this element.
		$classes = $field->getClassString();

		$buf = "<div class=\"row\">\n<div class=\"small-12 columns\">\n"; //Label Start

		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\" class=\"error\">";
		}
		else
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\">";
		}

		if ($field->isRequired() == 1)
		{
			$buf .= "<b>";
			$buf .= $field->getLabel();
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $field->getLabel();
		}

		$buf .= "</label>\n";
		$buf .= "</div>\n"; //Label End

		if (strlen($field->getInstructions()) >= 1)
		{
			$buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
			$buf .= $field->getInstructions();
			$buf .= "</div>"; //Instructions End
		}

		$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label class=\"error\">";
		}

		$buf .= $field->field();

		if (count($field->getErrors()) != 0)
		{
			$buf .= "</label>";
			$buf .= "<small class=\"error\">";
			foreach ($field->getErrors() as $error)
			{
				foreach ($error as $message)
				{
					$buf .= "- $message<br>\n";
				}
			}
			$buf .= "</small>";
		}
		$buf .= "</div>\n"; //Field End
		$buf .= "</div>\n"; //Row End
		return $buf;
	}

	public function TextareaElement(TextareaElement $field)
	{
		$buf = "<div class=\"row\">\n"; //Row Start
		$buf .= "<div class=\"small-12 columns\">\n"; //Label Start

		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\" class=\"error\">";
		}
		else
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\">";
		}

		if ($field->isRequired() == 1)
		{
			$buf .= "<b>";
			$buf .= $field->getLabel();
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $field->getLabel();
		}

		$buf .= "</label>\n";

		$buf .= "</div>\n"; //Label End

		if (strlen($field->getInstructions()) >= 1)
		{
			$buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
			$buf .= $field->getInstructions();
			$buf .= "</div>\n"; //Instructions End
		}

		$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label class=\"error\">";
		}

		$buf .= $field->field();

		if (count($field->getErrors()) != 0)
		{
			$buf .= "</label>";
			$buf .= "<small class=\"error\">";
			foreach ($field->getErrors() as $error)
			{
				foreach ($error as $message)
				{
					$buf .= "- $message<br>\n";
				}
			}
			$buf .= "</small>";
		}
		$buf .= "</div>\n"; //Field End
		$buf .= "</div>\n"; //Row End
		return $buf;
	}

	public function PasswordElement(PasswordElement $field)
	{
		$buf = "<div class=\"row\">\n"; //Row Start
		$buf .= "<div class=\"small-12 columns\">\n"; //Label Start


		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\" class=\"error\">";
		}
		else
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\">";
		}

		if ($field->isRequired() == 1)
		{
			$buf .= "<b>";
			$buf .= $field->getLabel();
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $field->getLabel();
		}

		$buf .= "</label>\n";

		$buf .= "</div>\n"; //Label End

		if (strlen($field->getInstructions()) >= 1)
		{
			$buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
			$buf .= $field->getInstructions();
			$buf .= "</div>\n"; //Instructions End
		}

		$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label class=\"error\">";
		}

		$buf .= $field->field();

		if (count($field->getErrors()) != 0)
		{
			$buf .= "</label>";
			$buf .= "<small class=\"error\">";
			foreach ($field->getErrors() as $error)
			{
				foreach ($error as $message)
				{
					$buf .= "- $message<br>\n";
				}
			}
			$buf .= "</small>";
		}
		$buf .= "</div>\n"; //Field End
		$buf .= "</div>\n"; //Row end

		return $buf;
	}

	public function HiddenElement(HiddenElement $field)
	{
		$buf = "<div class=\"row hide\">\n"; //Row Start
		$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
		$buf .= $field->field();
		$buf .= "</div>\n"; //Field End
		$buf .= "</div>\n"; //Row end
		return $buf;
	}

	public function SelectElement(SelectElement $field)
	{
		$buf = "<div class=\"row\">\n"; //Row Start
		$buf .= "<div class=\"small-12 columns\">\n"; //Label Start

		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\" class=\"error\">";
		}
		else
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\">";
		}

		if ($field->isRequired() == 1)
		{
			$buf .= "<b>";
			$buf .= $field->getLabel();
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $field->getLabel();
		}

		$buf .= "</label>\n";

		$buf .= "</div>\n"; //Label End

		if (strlen($field->getInstructions()) >= 1)
		{
			$buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
			$buf .= $field->getInstructions();
			$buf .= "</div>\n"; //Instructions End
		}

		$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label class=\"error\">";
		}

		$buf .= $field->field();

		if (count($field->getErrors()) != 0)
		{
			$buf .= "</label>";
			$buf .= "<small class=\"error\">";
			foreach ($field->getErrors() as $error)
			{
				foreach ($error as $message)
				{
					$buf .= "- $message<br>\n";
				}
			}
			$buf .= "</small>";
		}
		$buf .= "</div>\n"; //Field End
		$buf .= "</div>\n"; //Row End
		return $buf;
	}

	public function CheckboxElement(CheckboxElement $field)
	{

		$field->addClass('row');

		$classes = $field->getClassString();

		$buf = "<div$classes>\n"; //Row Start
		$buf .= "<div class=\"small-12 columns\">\n"; //Label Start

		if (count($field->getErrors()) != 0)
		{
			$buf .= "\t<label for=\"" . $this->escape($field->getId()) . "\" class=\"error\">";
		}
		else
		{
			$buf .= "\t<label for=\"" . $this->escape($field->getId()) . "\">";
		}

		if ($field->isRequired() == 1)
		{
			$buf .= "<b>";
			$buf .= $field->getLabel();
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $field->getLabel();
		}

		$buf .= "</label>\n";

		$buf .= "</div>\n"; //Label End

		if (strlen($field->getInstructions()) >= 1)
		{
			$buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
			$buf .= $field->getInstructions();
			$buf .= "</div>\n"; //Instructions End
		}


		$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label class=\"error\">";
		}

		$buf .= $field->field();

		if (count($field->getErrors()) != 0)
		{
			$buf .= "</label>";
			$buf .= "<small class=\"error\">";
			foreach ($field->getErrors() as $error)
			{
				foreach ($error as $message)
				{
					$buf .= "- $message<br>\n";
				}
			}
			$buf .= "</small>";
		}
		$buf .= "</div>\n"; //Field End
		$buf .= "</div>\n"; //Row end

		return $buf;
	}

	public function CheckboxgroupElement(CheckboxGroupElement $field)
	{

		$field->addClass('row');

		$buf = '';
		$classes = $field->getClassString();

		$buf .= "<div $classes>\n"; //Row Start
		$buf .= "<div class=\"small-12 columns\">\n"; //Label Start


		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\" class=\"error\">";
		}
		else
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\">";
		}

		if ($field->isRequired() == 1)
		{
			$buf .= "<b>";
			$buf .= $field->getLabel();
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $field->getLabel();
		}

		$buf .= "</label>\n";

		$buf .= "</div>\n"; //Label End

		if (strlen($field->getInstructions()) >= 1)
		{
			$buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
			$buf .= $field->getInstructions();
			$buf .= "</div>\n"; //Instructions End
		}

		$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label class=\"error\">";
		}

		$buf .= $field->field();
		$buf .= $field->getInstructions();

		if (count($field->getErrors()) != 0)
		{
			$buf .= "</label>";
			$buf .= "<small class=\"error\">";
			foreach ($field->getErrors() as $error)
			{
				foreach ($error as $message)
				{
					$buf .= "- $message<br>\n";
				}
			}
			$buf .= "</small>";
		}
		$buf .= "</div>\n"; //Field End
		$buf .= "</div>\n"; //Row end

		return $buf;
	}

	public function RadioElement(RadioElement $field)
	{

		$field->addClass('row');
		$buf = '';
		$classes = $field->getClassString();

		$buf .= "<div$classes>\n"; //Row Start
		$buf .= "\t<div class=\"small-12 columns\">\n"; //Label Start


		if (count($field->getErrors()) != 0)
		{
			$buf .= "\t\t<label for=\"" . $this->escape($field->getId()) . "\" class=\"error\">";
		}
		else
		{
			$buf .= "\t\t<label for=\"" . $this->escape($field->getId()) . "\">";
		}

		if ($field->isRequired() == 1)
		{
			$buf .= "<b>";
			$buf .= $field->getLabel();
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $field->getLabel();
		}

		$buf .= "</label>\n";

		$buf .= "\t</div>\n"; //Label End

		if (strlen($field->getInstructions()) >= 1)
		{
			$buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
			$buf .= $field->getInstructions();
			$buf .= "</div>\n"; //Instructions End
		}

		$buf .= "\n<div class=\"small-12 columns\">\n"; //Field Start
		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label class=\"error\">";
		}

		$buf .= $field->field();

		if (count($field->getErrors()) != 0)
		{
			$buf .= "</label>";
			$buf .= "<small class=\"error\">";
			foreach ($field->getErrors() as $error)
			{
				foreach ($error as $message)
				{
					$buf .= "- $message<br>\n";
				}
			}
			$buf .= "</small>";
		}
		$buf .= "\t</div>\n"; //Field End
		$buf .= "</div>\n"; //Row end

		return $buf;
	}


	public function FileElement(FileElement $field)
	{
		$field->addClass('');

		$buf = "<div class=\"row\">\n"; //Row Start
		$buf .= "<div class=\"small-12 columns\">\n"; //Label Start


		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\" class=\"error\">";
		}
		else
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\">";
		}

		if ($field->isRequired() == 1)
		{
			$buf .= "<b>";
			$buf .= $field->getLabel();
			$buf .= "</b> <small>(<i>Required</i>)</small>";
		}
		else
		{
			$buf .= $field->getLabel();
		}

		$buf .= "</label>\n";

		$buf .= "</div>\n"; //Label End

		if (strlen($field->getInstructions()) >= 1)
		{
			$buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
			$buf .= $field->getInstructions();
			$buf .= "</div>\n"; //Instructions End
		}

		$buf .= "<div class=\"small-12 columns\">\n"; //Field Start
		if (count($field->getErrors()) != 0)
		{
			$buf .= "<label class=\"error\">";
		}

		$buf .= $field->field();
		$buf .= $field->getInstructions();

		if (count($field->getErrors()) != 0)
		{
			$buf .= "</label>";
			$buf .= "<small class=\"error\">";
			foreach ($field->getErrors() as $error)
			{
				foreach ($error as $message)
				{
					$buf .= "- $message<br>\n";
				}
			}
			$buf .= "</small>";
		}
		$buf .= "</div>\n"; //Field End
		$buf .= "</div>\n"; //Row end

		return $buf;
	}

	public function SubmitElement(SubmitElement $field)
	{
		$field->addClass('button');
		$buf = '<div class="row">';
		$buf .= '<div class="small-12 columns">';
		if (isset($this->label))
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\"" . $field->getClassString() . ">" . $field->getLabel() . "</label>\n";
		}
		$buf .= $field->field();
		$buf .= "</div>"; //End column
		$buf .= "</div>"; //End Row
		return $buf;
	}

	public function ButtonElement(ButtonElement $field)
	{
		$field->addClass('button');
		$buf = '<div class="row">';
		$buf .= "\t<div class=\"small-12 columns\">";
		if (isset($this->label))
		{
			$buf .= "\t\t<label for=\"" . $this->escape($field->getId()) . "\"" . $field->getClassString() . ">" . $field->getLabel() . "</label>\n";
		}
		$buf .= $field->field();
		$buf .= "\t</div>";
		$buf .= "</div>";
		return $buf;
	}

	public function ImageElement(ImageElement $field)
	{
		$buf = '';

		$classes = $field->getClassString();
		$buf .= '<div class="' . $classes . ' row">\n';
		$buf .= '<div class="small-12 columns">\n';
		if (isset($this->label))
		{
			$buf .= "<label for=\"" . $this->escape($field->getId()) . "\"" . $field->getClassString() . ">" . $field->getLabel() . "</label>\n";
		}
		$buf .= $field->field();
		$buf .= "</div>\n";

		return $buf;
	}

}