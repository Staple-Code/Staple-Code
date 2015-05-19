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

use Staple\Form\ButtonElement;
use Staple\Form\CheckboxElement;
use Staple\Form\CheckboxGroupElement;
use Staple\Form\FileElement;
use Staple\Form\Form;
use Staple\Form\HiddenElement;
use Staple\Form\ImageElement;
use Staple\Form\PasswordElement;
use Staple\Form\RadioElement;
use Staple\Form\SelectElement;
use Staple\Form\SubmitElement;
use Staple\Form\TextareaElement;
use Staple\Form\TextElement;
use Staple\Form\Validate\EqualValidator;
use Staple\Form\Validate\InArrayValidator;
use Staple\Form\Validate\LengthValidator;
use Staple\Form\ViewAdapters\ElementViewAdapter;

class MyViewAdapter extends ElementViewAdapter
{
    function TextElement(TextElement $field)
    {
        // TODO: Implement TextElement() method.
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

class FormTest extends \PHPUnit_Framework_TestCase
{
    private function getCustomViewAdapter()
    {
        return new MyViewAdapter();
    }

    private function getFormObject($name = NULL)
    {
        return new Form($name);
    }

    private function getComplexTestForm()
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

        return $form;
    }

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
     * Test that a view adapter can be created and set on the form object.
     */
    public function testFormSetViewAdapter()
    {
        $form = $this->getFormObject('myForm');
        $form->setElementViewAdapter($this->getCustomViewAdapter());

        $this->assertInstanceOf('Staple\\Form\\ViewAdapters\\ElementViewAdapter',$form->getElementViewAdapter());
        $this->assertInstanceOf('Staple\\Tests\\MyViewAdapter',$form->getElementViewAdapter());
    }

    /**
     * Test the creation of a TextElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateTextElement()
    {
        $field = Form::textElement('MyField','My Field')
                ->setValue('Field Value');

        $this->assertInstanceOf('Staple\\Form\\TextElement',$field);
        $this->assertEquals('MyField',$field->getName());
        $this->assertEquals('My Field',$field->getLabel());
        $this->assertEquals('Field Value',$field->getValue());
    }

    /**
     * Test the creation of a ButtonElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateButtonElement()
    {
        $field = Form::buttonElement('MyField','My Field')
            ->setValue('Field Value');

        $this->assertInstanceOf('Staple\\Form\\ButtonElement',$field);
        $this->assertEquals('MyField',$field->getName());
        $this->assertEquals(null,$field->getLabel());
        $this->assertEquals('Field Value',$field->getValue());
    }

    /**
     * Test the creation of a SubmitElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateSubmitElement()
    {
        $field = Form::submitElement('MyField','My Field')
            ->setValue('Field Value');

        $this->assertInstanceOf('Staple\\Form\\SubmitElement',$field);
        $this->assertEquals('MyField',$field->getName());
        $this->assertEquals(null,$field->getLabel());
        $this->assertEquals('Field Value',$field->getValue());
    }

    /**
     * Test the creation of a HiddenElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateHiddenElement()
    {
        $field = Form::hiddenElement('MyField','My Field')
            ->setValue('Field Value');

        $this->assertInstanceOf('Staple\\Form\\HiddenElement',$field);
        $this->assertEquals('MyField',$field->getName());
        $this->assertEquals(null,$field->getLabel());
        $this->assertEquals('Field Value',$field->getValue());
    }

    /**
     * Test the creation of a FileElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateFileElement()
    {
        $this->markTestIncomplete();
    }

    /**
     * Test the creation of a PasswordElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreatePasswordElement()
    {
        $field = Form::passwordElement('MyField','My Field')
            ->setValue('Field Value');

        $this->assertInstanceOf('Staple\\Form\\PasswordElement',$field);
        $this->assertEquals('MyField',$field->getName());
        $this->assertEquals('My Field',$field->getLabel());
        $this->assertEquals('Field Value',$field->getValue());
    }

    /**
     * Test the creation of a RadioElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateRadioElement()
    {
        $this->markTestIncomplete();
    }

    /**
     * Test the creation of a SelectElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateSelectElement()
    {
        $this->markTestIncomplete();
    }

    /**
     * Test the creation of a TextareaElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateTextareaElement()
    {
        $this->markTestIncomplete();
    }

    /**
     * Test the creation of a ImageElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateImageElement()
    {
        $this->markTestIncomplete();
    }

    /**
     * Test the creation of a CheckboxElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateCheckboxElement()
    {
        $this->markTestIncomplete();
    }

	/**
	 * Test that a form can be fully created and validated.
	 */
	public function testFullFormWithValidation()
	{
		$form = $this->getComplexTestForm();

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
