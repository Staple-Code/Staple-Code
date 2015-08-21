<?php
/**
 * Created by PhpStorm.
 * User: adam
 * Date: 3/26/15
 * Time: 11:36 AM
 *
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

namespace Staple\Form\ViewAdapters;

use Staple\Form\ButtonElement;
use Staple\Form\CheckboxElement;
use Staple\Form\CheckboxGroupElement;
use Staple\Form\FileElement;
use Staple\Form\HiddenElement;
use Staple\Form\ImageElement;
use Staple\Form\PasswordElement;
use Staple\Form\RadioElement;
use Staple\Form\SelectElement;
use Staple\Form\SubmitElement;
use Staple\Form\TextareaElement;
use Staple\Form\TextElement;

class BootstrapViewAdapter extends ElementViewAdapter
{
	function TextElement(TextElement $field)
	{
		//Add form-control class for optimal element positioning.
		$field->addClass('form-control');

		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}

	function TextareaElement(TextareaElement $field)
	{
		//Add form-control class for optimal element positioning.
		$field->addClass('form-control');

		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}

	function PasswordElement(PasswordElement $field)
	{
		//Add form-control class for optimal element positioning.
		$field->addClass('form-control');

		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}

	function HiddenElement(HiddenElement $field)
	{
		$field->addClass('form-control');
		$buf = "\n<div class=\"form-group\">\n"; //Start Form-Group
		$buf .= $field->field();
		$buf .= "\n</div>"; //End Form-Group
		return $buf;
	}

	function SelectElement(SelectElement $field)
	{
		//Add form-control class for optimal element positioning.
		$field->addClass('form-control');

		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}

	function CheckboxgroupElement(CheckboxGroupElement $field)
	{
		//Add form-control class for optimal element positioning.
		$field->addClass('form-control');

		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}

	function CheckboxElement(CheckboxElement $field)
	{
		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "<div class=\"checkbox has-error\">\n"; //Start Checkbox
		}
		else
		{
			$buf = "<div class=\"checkbox\">\n"; //Start Checkbox
		}

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "<p class=\"text-muted\">\n";
			$buf .= $field->getInstructions() . "\n";
			$buf .= "</p>\n";
		}

		//Field Label
		$buf .= "\t<label class=\"control-label\">\n";
		//Add field to buffer
		$buf .= $field->field();
		$buf .= "\t" . $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>\n"; //End Checkbox

		return $buf;
	}

	function RadioElement(RadioElement $field)
	{
		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}

	function FileElement(FileElement $field)
	{
		//Add form-control class for optimal element positioning.
		$field->addClass('form-control');

		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}

	function SubmitElement(SubmitElement $field)
	{
		$field->addClass('btn');
		$buf = "\n<div class=\"form-group\">\n"; //Start Form-Group
		$buf .= $field->field();
		$buf .= "</div>"; //End Form-Group
		return $buf;
	}

	function ButtonElement(ButtonElement $field)
	{
		//Add form-control class for optimal element positioning.
		$field->addClass('btn');

		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}

	function ImageElement(ImageElement $field)
	{
		//Add form-control class for optimal element positioning.
		$field->addClass('form-control');

		//Build field buffer to be returned.
		if (count($field->getErrors()) > 0)
		{
			$buf = "\n<div class=\"form-group has-error\">"; //Start Form-Group
		}
		else
		{
			$buf = "\n<div class=\"form-group\">"; //Start Form-Group
		}

		//Field Label
		$buf .= "\n\t<label class=\"control-label\">";
		$buf .= $field->getLabel();

		//Check if field is required
		if ($field->isRequired())
		{
			$buf .= " <small>(Required)</small>";
		}
		$buf .= "</label>\n";

		//Add field instructions
		if (count($field->getInstructions()) > 0)
		{
			$buf .= "\n<p class=\"text-muted\">";
			$buf .= "\n" . $field->getInstructions();
			$buf .= "\n</p>";
		}

		//Add field to buffer
		$buf .= $field->field();

		//Generate error messages to be displayed below field.
		if (count($field->getErrors()) > 0)
		{
			foreach ($field->getErrors() as $error)
			{
				$buf .= "<ul class=\"list-group\">";
				foreach ($error as $message)
				{
					$buf .= "<li class=\"list-group-item list-group-item-danger\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> $message</li>";
				}
				$buf .= "</ul>";
			}
		}

		$buf .= "</div>"; //End Form-Group

		return $buf;
	}


}
