<?php
use Staple\Form\Form;
use Staple\Form\PasswordElement;
use Staple\Form\SubmitElement;
use Staple\Form\TextareaElement;
use Staple\Form\TextElement;
use Staple\Form\Validate\LengthValidator;

/**
 * @author Adam Day
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

class testForm extends Form
{
    public function _start()
    {
        $this->setName('test')
            ->setAction($this->link(array('test','index')));

        $text = new TextElement('text','Text Element');

        $requiredField = new TextElement('requiredField','Required Field');
        $requiredField->setRequired()
            ->addValidator(new LengthValidator('1','30'))
            ->addAttrib('placeholder','30 character limit');

        $password = new PasswordElement('password','Password Element');
        $password->setRequired();

        $textarea = new TextareaElement('textarea','Textarea Element');
        $textarea->addAttrib('style','height:200px;')
            ->setRequired()
            ->addValidator(new LengthValidator(0,1000))
            ->addAttrib('placeholder','1000 character limit')
            ->setInstructions("Here is the test for the instructions");

        $submit = new SubmitElement('submit','Submit Element');

        $this->addField($text, $requiredField, $password, $textarea, $submit);
    }
}