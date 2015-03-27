<?php
use Staple\Form\Form;
use Staple\Form\SubmitElement;
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
            ->setAction($this->link(array('index','index')));

        $test = new TextElement('test','Test');
        $test->setRequired()
            ->addValidator(new LengthValidator(1,10));

        $submit = new SubmitElement('submit','Submit');

        $this->addField($test, $submit);
    }

}