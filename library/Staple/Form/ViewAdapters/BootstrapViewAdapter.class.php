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
        $classes = $field->getClassString();
        $buf = "\n<div class=\"row\">\n<div class=\"col-md-12\">\n"; //Label Start

        if(count($field->getErrors()) != 0)
        {
            $buf .= "<div class=\"form-group has-error\">";
        }
        else
        {
            $buf .= "<div class=\"form-group\">";
        }

        if($field->isRequired() == 1)
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

        if(strlen($field->getInstructions()) >= 1)
        {
            $buf .= "<div class=\"small-12 columns\">\n"; //Instructions Start
            $buf .= $field->getInstructions();
            $buf .= "</div>"; //Instructions End
        }

        $buf .= "<div class=\"small-12 columns\">\n"; //Field Start
        if(count($field->getErrors()) != 0)
        {
            $buf .= "<label class=\"error\">";
        }

        $buf .= $field->field();

        if(count($field->getErrors()) != 0)
        {
            $buf .= "</label>";
            $buf .= "<small class=\"error\">";
            foreach($field->getErrors() as $error)
            {
                foreach($error as $message)
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

    function TextareaElement(TextareaElement $field)
    {
        // TODO: Implement TextareaElement() method.
    }

    function PasswordElement(PasswordElement $field)
    {
        // TODO: Implement PasswordElement() method.
    }

    function HiddenElement(HiddenElement $field)
    {
        // TODO: Implement HiddenElement() method.
    }

    function SelectElement(SelectElement $field)
    {
        // TODO: Implement SelectElement() method.
    }

    function CheckboxgroupElement(CheckboxGroupElement $field)
    {
        // TODO: Implement CheckboxgroupElement() method.
    }

    function CheckboxElement(CheckboxElement $field)
    {
        // TODO: Implement CheckboxElement() method.
    }

    function RadioElement(RadioElement $field)
    {
        // TODO: Implement RadioElement() method.
    }

    function FileElement(FileElement $field)
    {
        // TODO: Implement FileElement() method.
    }

    function SubmitElement(SubmitElement $field)
    {
        // TODO: Implement SubmitElement() method.
    }

    function ButtonElement(ButtonElement $field)
    {
        // TODO: Implement ButtonElement() method.
    }

    function ImageElement(ImageElement $field)
    {
        // TODO: Implement ImageElement() method.
    }


}
