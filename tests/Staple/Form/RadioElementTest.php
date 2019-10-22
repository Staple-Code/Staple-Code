<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 9/23/2015
 * Time: 2:30 PM
 */

namespace Staple\Tests;


use PHPUnit\Framework\TestCase;
use Staple\Form\RadioElement;
use Staple\Validate\AlnumValidator;
use Staple\Validate\DateValidator;
use Staple\Validate\EmailValidator;
use Staple\Validate\LengthValidator;
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;
use Staple\Validate\InArrayValidator;

class RadioElementTest extends TestCase
{
	const STANDARD_BUILD = "<div class=\"form_element element_radiogroup\" id=\"TestRadioElement_element\">\n\t<label class=\"form_element element_radiogroup\">Select A City</label>\n\t<div class=\"form_radio\" id=\"TestRadioElement_1_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_1\" value=\"1\" class=\"form_element element_radiogroup\">\n\t\t<label for=\"TestRadioElement_1\">Las Vegas</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_2_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_2\" value=\"2\" class=\"form_element element_radiogroup\">\n\t\t<label for=\"TestRadioElement_2\">New York</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_3_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_3\" value=\"3\" class=\"form_element element_radiogroup\">\n\t\t<label for=\"TestRadioElement_3\">Atlanta</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_4_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_4\" value=\"4\" class=\"form_element element_radiogroup\">\n\t\t<label for=\"TestRadioElement_4\">Orlando</label>\n\t</div>\n</div>\n";
	const FOUNDATION_BUILD = "<div class=\"row\">\n\t<div class=\"small-12 columns\">\n\t\t<label>Select A City</label>\n\t</div>\n\t<div class=\"small-12 columns\">\n\t\t<div class=\"form_radio\" id=\"TestRadioElement_1_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_1\" value=\"1\">\n\t\t<label for=\"TestRadioElement_1\">Las Vegas</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_2_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_2\" value=\"2\">\n\t\t<label for=\"TestRadioElement_2\">New York</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_3_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_3\" value=\"3\">\n\t\t<label for=\"TestRadioElement_3\">Atlanta</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_4_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_4\" value=\"4\">\n\t\t<label for=\"TestRadioElement_4\">Orlando</label>\n\t</div>\n\t</div>\n</div>\n";
	const BOOTSTRAP_BUILD = "<div class=\"form-group\">\n\t<label class=\"control-label\">Select A City</label>\n\t<div class=\"form_radio\" id=\"TestRadioElement_1_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_1\" value=\"1\">\n\t\t<label for=\"TestRadioElement_1\">Las Vegas</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_2_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_2\" value=\"2\">\n\t\t<label for=\"TestRadioElement_2\">New York</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_3_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_3\" value=\"3\">\n\t\t<label for=\"TestRadioElement_3\">Atlanta</label>\n\t</div>\n\t<div class=\"form_radio\" id=\"TestRadioElement_4_div\">\n\t\t<input type=\"radio\" name=\"TestRadioElement\" id=\"TestRadioElement_4\" value=\"4\">\n\t\t<label for=\"TestRadioElement_4\">Orlando</label>\n\t</div>\n</div>\n";
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

		$buf =  $element->build();

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

		$buf =  $element->build();

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

		$buf =  $element->build();

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
		$element->addValidator(LengthValidator::create(10));
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
		$element->addValidator(AlnumValidator::create());
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
		$element->addValidator(DateValidator::create());
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
		$element->addValidator(EmailValidator::create());
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
		$element->addValidator(InArrayValidator::create(null, array_keys($array)));
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
