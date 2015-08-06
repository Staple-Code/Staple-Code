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
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\ElementViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;

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
        $field = Form::fileElement('MyField','My Field')
            ->setValue('/filefolder/filelocation.html');

        $this->assertInstanceOf('Staple\\Form\\FileElement',$field);
        $this->assertEquals('MyField',$field->getName());
        $this->assertEquals('My Field',$field->getLabel());
        $this->assertEquals('/filefolder/filelocation.html',$field->getValue());
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
		$buttons = [
			1	=>	'Camel',
			2	=>	'Horse',
			3	=>	'Car',
			4	=>	'Train',
			5	=>	'Plane'
		];
		$field = Form::radioElement('transportMethod','Favorite Method of Travel')
			->addButtonsArray($buttons)
			->setValue(5);

		$this->assertInstanceOf('Staple\\Form\\RadioElement',$field);
		$this->assertEquals('transportMethod',$field->getName());
		$this->assertEquals('Favorite Method of Travel',$field->getLabel());
		$this->assertEquals(5,$field->getValue());
		$this->assertSame($buttons,$field->getButtons());
    }

    /**
     * Test the creation of a SelectElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateSelectElement()
    {
		$options = [
			1	=>	'New York',
			2	=>	'Boston',
			3	=>	'Los Angeles',
			4	=>	'Portland',
			5	=>	'Austin'
		];
		$field = Form::selectElement('city','Cities')
			->addOptionsArray($options)
			->setValue(3);

		$this->assertInstanceOf('Staple\\Form\\SelectElement',$field);
		$this->assertEquals('city',$field->getName());
		$this->assertEquals('Cities',$field->getLabel());
		$this->assertEquals(3,$field->getValue());
		$this->assertSame($options,$field->getOptions());
    }

    /**
     * Test the creation of a TextareaElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateTextareaElement()
    {
        $field = Form::textareaElement('comments','Questions or Comments')
            ->setValue("The best site that ever existed!");

        $this->assertInstanceOf('Staple\\Form\\TextareaElement',$field);
        $this->assertEquals('comments',$field->getName());
        $this->assertEquals('Questions or Comments',$field->getLabel());
        $this->assertEquals("The best site that ever existed!",$field->getValue());
    }

    /**
     * Test the creation of a ImageElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateImageElement()
    {
        $field = Form::imageElement('submitImage')
            ->setSrc('images/button.jpg');

        $this->assertInstanceOf('Staple\\Form\\ImageElement',$field);
        $this->assertEquals('submitImage',$field->getName());
        $this->assertEquals('images/button.jpg',$field->getSrc());
    }

    /**
     * Test the creation of a CheckboxElement object from the short form methods.
     * @throws \Exception
     */
    public function testCreateCheckboxElement()
    {
        $field = Form::checkboxElement('emailList','Join our mailing list!')
            ->setChecked(true);

        $this->assertInstanceOf('Staple\\Form\\CheckboxElement',$field);
        $this->assertEquals('emailList',$field->getName());
        $this->assertEquals('Join our mailing list!',$field->getLabel());
        $this->assertEquals('1',$field->getValue());
        $this->assertEquals(true,$field->isChecked());
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

    public function testFormStandardBuild()
    {
		$form = $this->getComplexTestForm();
		$ident = $form->fields['ident']->getValue();
		$expectedOutput = "\n<form name=\"testform\" id=\"testform_form\" action=\"/test/form\" method=\"GET\">"
			."\n<div id=\"testform_div\">"
			."\n\n<div class=\"form-group\">\n\t<label class=\"control-label\">First Name <small>(Required)</small></label>"
			."\n\t<input type=\"text\" id=\"fname\" name=\"fname\" value=\"\" class=\"form_required form-control\">"
			."\n</div>"
			."\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Last Name</label>"
			."\n\t<input type=\"text\" id=\"lname\" name=\"lname\" value=\"\" class=\"form-control\">"
			."\n</div>\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Your Biography</label>"
			."\n\t<textarea rows=\"5\" cols=\"40\" id=\"bio\" name=\"bio\" class=\"form-control\"></textarea>"
			."\n</div>\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Year of Birth <small>(Required)</small></label>"
			."\n\t<select name=\"birthyear\" id=\"birthyear\" class=\"form_required form-control\">"
			."\n\t\t<option value=\"\"></option>"
			."\n\t\t<option value=\"1994\">1994</option>"
			."\n\t\t<option value=\"1995\">1995</option>"
			."\n\t\t<option value=\"1996\">1996</option>"
			."\n\t\t<option value=\"1997\">1997</option>"
			."\n\t\t<option value=\"1998\">1998</option>"
			."\n\t\t<option value=\"1999\">1999</option>"
			."\n\t\t<option value=\"2000\">2000</option>"
			."\n\t</select>"
			."\n</div>"
			."\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">I need to add a spouse:</label>"
			."\n\t<div class=\"form_radio\" id=\"spouse_0_div\">"
			."\n\t\t<input type=\"radio\" name=\"spouse\" id=\"spouse_0\" value=\"0\">"
			."\n\t\t<label for=\"spouse_0\">Yes</label>"
			."\n\t</div>"
			."\n\t<div class=\"form_radio\" id=\"spouse_1_div\">"
			."\n\t\t<input type=\"radio\" name=\"spouse\" id=\"spouse_1\" value=\"1\" checked>"
			."\n\t\t<label for=\"spouse_1\">No</label>"
			."\n\t</div>"
			."\n</div>"
			."\n<div class=\"form-group\">\t<input type=\"submit\" id=\"send\" name=\"send\" value=\"Send Query\" class=\"btn\">"
			."\n</div>\n"
			."\n<div class=\"form-group\">\t<input type=\"hidden\" id=\"ident\" name=\"ident\" value=\"$ident\">\n\n</div>\n</div>\n</form>\n";
		$output = $form->build();

		$this->assertEquals($expectedOutput, $output);
    }

	public function testFormBootstrapBuild()
	{
		$this->markTestIncomplete();
		$form = $this->getComplexTestForm();
		$form->setElementViewAdapter(new BootstrapViewAdapter());
		$ident = $form->fields['ident']->getValue();
		$expectedOutput = "\n<form name=\"testform\" id=\"testform_form\" action=\"/test/form\" method=\"GET\">"
			."\n<div id=\"testform_div\">"
			."\n\n<div class=\"form-group\">\n\t<label class=\"control-label\">First Name <small>(Required)</small></label>"
			."\n\t<input type=\"text\" id=\"fname\" name=\"fname\" value=\"\" class=\"form_required form-control\">"
			."\n</div>"
			."\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Last Name</label>"
			."\n\t<input type=\"text\" id=\"lname\" name=\"lname\" value=\"\" class=\"form-control\">"
			."\n</div>\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Your Biography</label>"
			."\n\t<textarea rows=\"5\" cols=\"40\" id=\"bio\" name=\"bio\" class=\"form-control\"></textarea>"
			."\n</div>\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Year of Birth <small>(Required)</small></label>"
			."\n\t<select name=\"birthyear\" id=\"birthyear\" class=\"form_required form-control\">"
			."\n\t\t<option value=\"\"></option>"
			."\n\t\t<option value=\"1994\">1994</option>"
			."\n\t\t<option value=\"1995\">1995</option>"
			."\n\t\t<option value=\"1996\">1996</option>"
			."\n\t\t<option value=\"1997\">1997</option>"
			."\n\t\t<option value=\"1998\">1998</option>"
			."\n\t\t<option value=\"1999\">1999</option>"
			."\n\t\t<option value=\"2000\">2000</option>"
			."\n\t</select>"
			."\n</div>"
			."\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">I need to add a spouse:</label>"
			."\n\t<div class=\"form_radio\" id=\"spouse_0_div\">"
			."\n\t\t<input type=\"radio\" name=\"spouse\" id=\"spouse_0\" value=\"0\">"
			."\n\t\t<label for=\"spouse_0\">Yes</label>"
			."\n\t</div>"
			."\n\t<div class=\"form_radio\" id=\"spouse_1_div\">"
			."\n\t\t<input type=\"radio\" name=\"spouse\" id=\"spouse_1\" value=\"1\" checked>"
			."\n\t\t<label for=\"spouse_1\">No</label>"
			."\n\t</div>"
			."\n</div>"
			."\n<div class=\"form-group\">\t<input type=\"submit\" id=\"send\" name=\"send\" value=\"Send Query\" class=\"btn\">"
			."\n</div>\n"
			."\n<div class=\"form-group\">\t<input type=\"hidden\" id=\"ident\" name=\"ident\" value=\"$ident\">\n\n</div>\n</div>\n</form>\n";
		$output = $form->build();

		$this->assertEquals('', $output);
	}

	public function testFormFoundationBuild()
	{
		$this->markTestIncomplete();
		$form = $this->getComplexTestForm();
		$form->setElementViewAdapter(new FoundationViewAdapter());
		$ident = $form->fields['ident']->getValue();
		$expectedOutput = "\n<form name=\"testform\" id=\"testform_form\" action=\"/test/form\" method=\"GET\">"
			."\n<div id=\"testform_div\">"
			."\n\n<div class=\"form-group\">\n\t<label class=\"control-label\">First Name <small>(Required)</small></label>"
			."\n\t<input type=\"text\" id=\"fname\" name=\"fname\" value=\"\" class=\"form_required form-control\">"
			."\n</div>"
			."\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Last Name</label>"
			."\n\t<input type=\"text\" id=\"lname\" name=\"lname\" value=\"\" class=\"form-control\">"
			."\n</div>\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Your Biography</label>"
			."\n\t<textarea rows=\"5\" cols=\"40\" id=\"bio\" name=\"bio\" class=\"form-control\"></textarea>"
			."\n</div>\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">Year of Birth <small>(Required)</small></label>"
			."\n\t<select name=\"birthyear\" id=\"birthyear\" class=\"form_required form-control\">"
			."\n\t\t<option value=\"\"></option>"
			."\n\t\t<option value=\"1994\">1994</option>"
			."\n\t\t<option value=\"1995\">1995</option>"
			."\n\t\t<option value=\"1996\">1996</option>"
			."\n\t\t<option value=\"1997\">1997</option>"
			."\n\t\t<option value=\"1998\">1998</option>"
			."\n\t\t<option value=\"1999\">1999</option>"
			."\n\t\t<option value=\"2000\">2000</option>"
			."\n\t</select>"
			."\n</div>"
			."\n<div class=\"form-group\">"
			."\n\t<label class=\"control-label\">I need to add a spouse:</label>"
			."\n\t<div class=\"form_radio\" id=\"spouse_0_div\">"
			."\n\t\t<input type=\"radio\" name=\"spouse\" id=\"spouse_0\" value=\"0\">"
			."\n\t\t<label for=\"spouse_0\">Yes</label>"
			."\n\t</div>"
			."\n\t<div class=\"form_radio\" id=\"spouse_1_div\">"
			."\n\t\t<input type=\"radio\" name=\"spouse\" id=\"spouse_1\" value=\"1\" checked>"
			."\n\t\t<label for=\"spouse_1\">No</label>"
			."\n\t</div>"
			."\n</div>"
			."\n<div class=\"form-group\">\t<input type=\"submit\" id=\"send\" name=\"send\" value=\"Send Query\" class=\"btn\">"
			."\n</div>\n"
			."\n<div class=\"form-group\">\t<input type=\"hidden\" id=\"ident\" name=\"ident\" value=\"$ident\">\n\n</div>\n</div>\n</form>\n";
		$output = $form->build();

		$this->assertEquals('', $output);
	}

	public function testFormCustomBuild()
	{
		$this->markTestIncomplete();
	}
}
