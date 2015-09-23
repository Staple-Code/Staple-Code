<?php
use Staple\Form\ButtonElement;
use Staple\Form\CheckboxElement;
use Staple\Form\CheckboxGroupElement;
use Staple\Form\FileElement;
use Staple\Form\Form;
use Staple\Form\HiddenElement;
use Staple\Form\PasswordElement;
use Staple\Form\RadioElement;
use Staple\Form\SelectElement;
use Staple\Form\SubmitElement;
use Staple\Form\TextareaElement;
use Staple\Form\TextElement;
use Staple\Form\Validate\InArrayValidator;
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
        $text->setRequired()
            ->addValidator(new LengthValidator(1,2));

        $textInstructions = new TextElement('textInstruction','Text Element with Instructions');
        $textInstructions->setRequired()
            ->addValidator(new LengthValidator(0,30))
            ->addInstructions('Here are the instructions for this element.');

        $requiredText = new TextElement('requiredText','Required Text Element');
        $requiredText->setRequired()
            ->addValidator(new LengthValidator('1','30'))
            ->addAttrib('placeholder','Required Text Element with length validator and placeholder')
            ->addInstructions('30 character limit');

        $password = new PasswordElement('password','Password Element');
        $password->setRequired()
            ->addValidator(new LengthValidator(1,30));

        $textarea = new TextareaElement('textarea','Textarea Element');
        $textarea->setRequired()
            ->addValidator(new LengthValidator(1,100))
            ->addAttrib('style','height:200px;')
            ->addAttrib('placeholder','Placeholder')
            ->addInstructions('Here are the instructions for this field.');

        $select = new SelectElement('select','Select Element');
        $select->addOptionsArray(
            array(
                "1"=>"Option 1",
                "2"=>"Option 2",
                "3"=>"Option 3",
                "4"=>"Option 4",
                "5"=>"Option 5"
            )
        )
        ->addInstructions('Here are the instructions for a select element.');

        $hidden = new HiddenElement('hidden','Hidden Element');

        $checkbox = new CheckboxElement('single','Single Checkbox Element');
        $checkbox->setRequired()
            ->addValidator(new InArrayValidator(array("single")))
            ->addInstructions('Here are a set of instructions');

        $checkboxGroup = new CheckboxGroupElement('checkboxGroup','Checkbox Group Element');
        $checkboxGroup->addCheckboxArray(
            array(new CheckboxElement('1','One'),
                new CheckboxElement('2','Two'),
                new CheckboxElement('3','Three'),
                new CheckboxElement('4','Four'),
                new CheckboxElement('5','Five'))
        );
        $checkboxGroup->setRequired()
            ->addValidator(new InArrayValidator(array("1","2","3","4","5")))
            ->addAttrib('placeholder','Placholder')
            ->addInstructions('Here are the instructions');

        $radio = new RadioElement('radio','Radio Element');
        $radio->addButtonsArray(
            array(
                "6"=>"Six",
                "7"=>"Seven",
                "8"=>"Eight",
                "9"=>"Nine",
                "10"=>"Ten"
            )
        )
        ->setRequired()
        ->addValidator(new InArrayValidator(array("6","7","8","9","10")))
        ->addInstructions('Instructions are here')
        ->addAttrib('placeholder','Placeholder');

        $file = new FileElement('file','File Element');
        $file->setRequired();

        $button = new ButtonElement('button','Button Element');

        $file = new FileElement('file','File Element');
        $file->setRequired();

        $button = new ButtonElement('button','Button Element');

        $submit = new SubmitElement('submit','Submit Element');

        $this->addField(
            $text,
            $textInstructions,
            $requiredText,
            $password,
            $textarea,
            $select,
            $hidden,
            $checkbox,
            $checkboxGroup,
            $radio,
            $file,
            $button,
            $submit
        );
    }
}