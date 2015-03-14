<?php
/**
 * Unit Tests for \Staple\Form\Form object
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

namespace Staple\Tests;

use Staple\Form\Form;
use Staple\Form\RadioElement;
use Staple\Form\SelectElement;
use Staple\Form\SubmitElement;
use Staple\Form\TextareaElement;
use Staple\Form\TextElement;
use Staple\Form\Validate\EqualValidator;
use Staple\Form\Validate\InArrayValidator;
use Staple\Form\Validate\LengthValidator;

class FormTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test for variations of form creation methods
	 */
	public function testFormCreation()
	{
		$form1 = new Form('testform');
		$form2 = new Form();
		$form3 = Form::create('MyTestForm');
		$form4 = new Form('myForm','test/action');

		$this->assertInstanceOf('Form',$form1);
		$this->assertInstanceOf('Form',$form2);
		$this->assertInstanceOf('Form',$form3);
		$this->assertInstanceOf('Form',$form4);
	}

	/**
	 * Test that a form can be fully created and validated.
	 */
	public function testFullFormWithValidation()
	{
		$form = new Form('testform');
		$form->setAction('/test/form')
			->setMethod("GET")
			->addField(TextElement::Create('fname','First Name')
				->setRequired()
				->addValidator(new LengthValidator(10,5)))
			->addField(new TextElement('lname','Last Name'))

			->addField(TextareaElement::Create('bio','Your Biography')
				->setRows(5)
				->setCols(40)
				->addValidator(new LengthValidator(5,5000)))
			->addField(
				SelectElement::Create('birthyear','Year of Birth')
					->setRequired()
					->addOptionsArray(array('','1994','1995','1996','1997','1998','1999','2000'),true)
					->addValidator(new InArrayValidator(array('','1994','1995','1996','1997','1998','1999','2000')))
			)
			->addField(RadioElement::Create('spouse','I need to add a spouse:')
				->addButtonsArray(array('Yes','No'))
				->setValue(1)
				->addValidator(new EqualValidator('Yes'))
			)
			->addField(new SubmitElement('send','Send Query'));

		$dataArray = [
			'fname'		=>	'FirstName',
			'bio'		=>	'This is my biography.',
			'birthyear'	=>	'1996',
			'spouse' 	=> 	'Yes',
		];

		$form->addData($dataArray);
		$this->assertTrue($form->validate());
	}
}
