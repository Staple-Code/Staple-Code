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

use Staple\Dev;
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
        Dev::dump($field);

        $classes = "";

        if(count($field->getErrors()) > 0)
        {
            $field->addClass('has-error');
        }

        $field->addClass('form-control');

        $buf = "\n<div class=\"form-group\">"; //Start Form-Group

        $buf .= "\n\t<label $classes>";
        $buf .= $field->getLabel();

        if($field->isRequired())
        {
            $buf .= " (Required)";
        }
        $buf .= "</label>\n";

        $buf .= $field->field();

        if(count($field->getErrors()) > 0)
        {
            foreach($field->getErrors() as $error)
            {
                $buf .= "\n<span class=\"help-block\">";
                $buf .= "\t\n<ul>";
                foreach($error as $message)
                {
                    $buf .= "\t\t\n<li>$message</li>";
                }
                $buf .= "\t\n</ul>";
                $buf .= "\n</span>";
            }
        }

        $buf .= "</div>"; //End Form-Group

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
        $field->addClass('btn');
        $buf = "\n<div class=\"form-group\">"; //Start Form-Group
        $buf .= $field->field();
        $buf .= "</div>"; //End Form-Group
        return $buf;
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
