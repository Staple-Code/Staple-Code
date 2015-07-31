<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 7/31/2015
 * Time: 11:20 AM
 */

namespace Staple\Tests;


use Staple\Form\TextElement;
use Staple\Form\Validate\AlnumValidator;
use Staple\Form\Validate\DateValidator;
use Staple\Form\Validate\EmailValidator;
use Staple\Form\Validate\LengthValidator;
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;

class TextElementTest extends \PHPUnit_Framework_TestCase
{
	const STANDARD_BUILD = "<div  class=\"form_element element_text\" id=\"TestTextElement_element\">\n\t<label for=\"TestTextElement\" class=\"form_element element_text\">My Test Text Element</label>\n\t<input type=\"text\" id=\"TestTextElement\" name=\"TestTextElement\" value=\"\" class=\"form_element element_text\">\n</div>\n";
	const FOUNDATION_BUILD = "\n<div class=\"row\">\n<div class=\"small-12 columns\">\n<label for=\"TestTextElement\">My Test Text Element</label>\n</div>\n<div class=\"small-12 columns\">\n\t<input type=\"text\" id=\"TestTextElement\" name=\"TestTextElement\" value=\"\">\n</div>\n</div>\n";
	const BOOTSTRAP_BUILD = "\n<div class=\"form-group\">\n\t<label class=\"control-label\">My Test Text Element</label>\n\t<input type=\"text\" id=\"TestTextElement\" name=\"TestTextElement\" value=\"\" class=\"form-control\">\n</div>";
	/**
	 * @return TextElement
	 */
	private function getTestTextElement()
	{
		return TextElement::create('TestTextElement','My Test Text Element');
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
		$element = $this->getTestTextElement();

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
		$element = $this->getTestTextElement();
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
		$element = $this->getTestTextElement();
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
		$element = $this->getTestTextElement();

		$element->setValue('TestValue');

		$this->assertEquals('TestValue',$element->getValue());
	}

	/**
	 * Test base validator to ensure that it works properly on this field.
	 * @test
	 */
	public function testBaseValidator()
	{
		$element = $this->getTestTextElement();

		//An element with no validators should individually assert true when asked if valid, no content and not required.
		$this->assertTrue($element->isValid());
		$element->setRequired(true);
		$this->assertFalse($element->isValid());
		$element->setValue('Value');
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
		$element = $this->getTestTextElement();

		//Validate Length
		$element->addValidator(LengthValidator::Create(10));
		$element->setValue('12345');
		$this->assertFalse($element->isValid());
		$element->setValue('1234567890');
		$this->assertTrue($element->isValid());
	}

	public function testAlphanumericValidator()
	{
		$element = $this->getTestTextElement();

		//Validate Alphanumeric
		$element->addValidator(AlnumValidator::Create());
		$element->setValue("This is a sentence.");
		$this->assertFalse($element->isValid());
		$element->setValue('MyUsername1');
		$this->assertTrue($element->isValid());
	}

	public function testDateValidator()
	{
		$element = $this->getTestTextElement();

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

	public function testEmailValidator()
	{
		$element = $this->getTestTextElement();

		//Validate Email Address
		$element->addValidator(EmailValidator::Create());
		$element->setValue("notemyemail");
		$this->assertFalse($element->isValid());
		$element->setValue('Thisemail@works.com');
		$this->assertTrue($element->isValid());
	}
}
