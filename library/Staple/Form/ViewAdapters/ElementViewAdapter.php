<?php
/**
 * The base class for all Form View Adapters in the framework
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


abstract class ElementViewAdapter
{
    use \Staple\Traits\Helpers;

	abstract function TextElement(TextElement $field);

	abstract function TextareaElement(TextareaElement $field);

	abstract function PasswordElement(PasswordElement $field);

	abstract function HiddenElement(HiddenElement $field);

    abstract function SelectElement(SelectElement $field);

	abstract function CheckboxgroupElement(CheckboxGroupElement $field);

	abstract function CheckboxElement(CheckboxElement $field);

    abstract function RadioElement(RadioElement $field);

    abstract function FileElement(FileElement $field);

    abstract function SubmitElement(SubmitElement $field);

    abstract function ButtonElement(ButtonElement $field);

    abstract function ImageElement(ImageElement $field);
}