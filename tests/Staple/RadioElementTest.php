<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 9/23/2015
 * Time: 2:30 PM
 */

namespace Staple\Tests;


use Staple\Form\RadioElement;
use Staple\Form\Validate\AlnumValidator;
use Staple\Form\Validate\DateValidator;
use Staple\Form\Validate\EmailValidator;
use Staple\Form\Validate\InArrayValidator;
use Staple\Form\Validate\LengthValidator;
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;

class RadioElementTest extends \PHPUnit_Framework_TestCase
{
	const STANDARD_BUILD = "<div class=\"form_element element_select\" id=\"TestSelectElement_element\">\n\t<label for=\"TestSelectElement\" class=\"form_element element_select\">My Test Select Element</label>\n\t<select name=\"TestSelectElement\" id=\"TestSelectElement\" class=\"form_element element_select\">\n\t\t<option value=\"1\">Las Vegas</option>\n\t\t<option value=\"2\">New York</option>\n\t\t<option value=\"3\">Atlanta</option>\n\t\t<option value=\"4\">Orlando</option>\n\t</select>\n</div>\n";
	const FOUNDATION_BUILD = "<div class=\"row\">\n<div class=\"small-12 columns\">\n<label for=\"TestSelectElement\">My Test Select Element</label>\n</div>\n<div class=\"small-12 columns\">\n\t<select name=\"TestSelectElement\" id=\"TestSelectElement\">\n\t\t<option value=\"1\">Las Vegas</option>\n\t\t<option value=\"2\">New York</option>\n\t\t<option value=\"3\">Atlanta</option>\n\t\t<option value=\"4\">Orlando</option>\n\t</select>\n</div>\n</div>\n";
	const BOOTSTRAP_BUILD = "\n<div class=\"form-group\">\n\t<label class=\"control-label\">My Test Select Element</label>\n\t<select name=\"TestSelectElement\" id=\"TestSelectElement\" class=\"form-control\">\n\t\t<option value=\"1\">Las Vegas</option>\n\t\t<option value=\"2\">New York</option>\n\t\t<option value=\"3\">Atlanta</option>\n\t\t<option value=\"4\">Orlando</option>\n\t</select>\n</div>";
	/**
	 * @return RadioElement
	 */
	private function getTestRadioElement()
	{
		return RadioElement::create('TestRadioElement','Select A City')
			->addButtonsArray([
				'1'	=>	'Las Vegas',
				'2'	=>	'New York',
				'3'	=>	'Atlanta',
				'4'	=>	'Orlando'
			]);
	}

	private function getFoundationViewAdapter()
	{
		return new FoundationViewAdapter();
	}

	private function getBootstrapViewAdapter()
	{
		return new BootstrapViewAdapter();
	}

	/**
	 * Standard Output Build Test
	 * @test
	 */
	public function testStandardBuild()
	{
		$element = $this->getTestRadioElement();

		ob_start();
		echo $element->build();
		$buf = ob_get_contents();
		ob_end_clean();

		$this->assertEquals(self::STANDARD_BUILD,$buf);
	}

	/**
	 * Test Foundation Build for this field.
	 * @test
	 */
	public function testFoundationBuild()
	{
		$element = $this->getTestRadioElement();
		$element->setElementViewAdapter($this->getFoundationViewAdapter());

		ob_start();
		echo $element->build();
		$buf = ob_get_contents();
		ob_end_clean();

		$this->assertEquals(self::FOUNDATION_BUILD,$buf);
	}

	/**
	 * Test Bootstrap Build for this field
	 * @test
	 */
	public function testBootstrapBuild()
	{
		$element = $this->getTestRadioElement();
		$element->setElementViewAdapter($this->getBootstrapViewAdapter());

		ob_start();
		echo $element->build();
		$buf = ob_get_contents();
		ob_end_clean();

		$this->assertEquals(self::BOOTSTRAP_BUILD,$buf);
	}

	/**
	 * Test that we can set and retrieve values from the object
	 * @test
	 * @throws \Exception
	 */
	public function testValueSetAndRetrieve()
	{
		$element = $this->getTestRadioElement();

		$element->setValue('4');

		$this->assertEquals('4',$element->getValue());
	}

	/**
	 * Test base validator to ensure that it works properly on this field.
	 * @test
	 */
	public function testBaseValidator()
	{
		$element = $this->getTestRadioElement();

		//An element with no validators should individually assert true when asked if valid, no content and not required.
		$this->assertTrue($element->isValid());
		$element->setRequired(true);
		$this->assertFalse($element->isValid());
		$element->setValue('3');
		$this->assertTrue($element->isValid());
		$element->setRequired(false);
	}

	/**
	 * Test that the length validator works properly with this field
	 * @test
	 * @throws \Exception
	 */
	public function testLengthValidator()
	{
		$element = $this->getTestRadioElement();

		//Validate Length
		$element->addValidator(LengthValidator::Create(10));
		$element->setValue('12345');
		$this->assertFalse($element->isValid());
		$element->setValue('1234567890');
		$this->assertTrue($element->isValid());
	}

	/**
	 * @test
	 */
	public function testAlphanumericValidator()
	{
		$element = $this->getTestRadioElement();

		//Validate Alphanumeric
		$element->addValidator(AlnumValidator::Create());
		$element->setValue("This is a sentence.");
		$this->assertFalse($element->isValid());
		$element->setValue('MyUsername1');
		$this->assertTrue($element->isValid());
	}

	/**
	 * @test
	 */
	public function testDateValidator()
	{
		$element = $this->getTestRadioElement();

		//Validate Dates
		$element->addValidator(DateValidator::Create());
		$element->setValue('now');
		$this->assertFalse($element->isValid());	//Date validation occurs with regex.
		$element->setValue('10/03/1996');
		$this->assertTrue($element->isValid());
		$element->setValue('9-4-1972');
		$this->assertTrue($element->isValid());
		$element->setValue('12-35-2007');
		$this->assertFalse($element->isValid());
		$element->setValue('1.1.1999');
		$this->assertTrue($element->isValid());
	}

	/**
	 * @test
	 */
	public function testEmailValidator()
	{
		$element = $this->getTestRadioElement();

		//Validate Email Address
		$element->addValidator(EmailValidator::Create());
		$element->setValue("notemyemail");
		$this->assertFalse($element->isValid());
		$element->setValue('Thisemail@works.com');
		$this->assertTrue($element->isValid());
	}

	/**
	 * Test the InArrayValidator applied to the RadioElement object
	 * @test
	 */
	public function testInArrayValidator()
	{
		$element = $this->getTestRadioElement();

		$array = [
			'1'	=>	'Las Vegas',
			'2'	=>	'New York',
			'3'	=>	'Atlanta',
			'4'	=>	'Orlando'
		];

		//Validate In Array
		$element->addValidator(InArrayValidator::Create(array_keys($array)));
		$element->setValue(4);
		$this->assertTrue($element->isValid());
		$element->setValue('2');
		$this->assertTrue($element->isValid());
		$element->setValue('New York');
		$this->assertFalse($element->isValid());
		$element->setValue(0);
		$this->assertFalse($element->isValid());
	}
}
