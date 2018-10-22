<?php
/**
 * Test Cases for Pager Class
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

namespace Staple\Tests;


use PHPUnit\Framework\TestCase;
use Staple\Form\TextareaElement;
use Staple\Validate\AlnumValidator;
use Staple\Validate\DateValidator;
use Staple\Validate\EmailValidator;
use Staple\Validate\LengthValidator;
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;

class TextAreaElementTest extends TestCase
{
	const STANDARD_BUILD = "<div class=\"form_element element_textarea\" id=\"TestTextAreaElement_element\">\n\t<label for=\"TestTextAreaElement\" class=\"form_element element_textarea\">Enter Some Text</label>\n\t<textarea rows=\"5\" cols=\"40\" id=\"TestTextAreaElement\" name=\"TestTextAreaElement\" class=\"form_element element_textarea\">Textarea Text.</textarea>\n</div>\n";
	const FOUNDATION_BUILD = "<div class=\"row\">\n\t<div class=\"small-12 columns\">\n\t\t<label for=\"TestTextAreaElement\">Enter Some Text</label>\n\t</div>\n\t<div class=\"small-12 columns\">\n\t\t<textarea rows=\"5\" cols=\"40\" id=\"TestTextAreaElement\" name=\"TestTextAreaElement\">Textarea Text.</textarea>\n\t</div>\n</div>\n";
	const BOOTSTRAP_BUILD = "<div class=\"form-group\">\n\t<label class=\"control-label\" for=\"TestTextAreaElement\">Enter Some Text</label>\n\t<textarea rows=\"5\" cols=\"40\" id=\"TestTextAreaElement\" name=\"TestTextAreaElement\" class=\"form-control\">Textarea Text.</textarea>\n</div>\n";
	/**
	 * @return TextAreaElement
	 */
	private function getTestTextElement()
	{
		return TextareaElement::create('TestTextAreaElement','Enter Some Text')->setValue('Textarea Text.')->setRows(5)->setCols(40);
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

		$buf =  $element->build();

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

		$buf =  $element->build();

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
		$element->setValue(NULL);

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
		$element->addValidator(LengthValidator::create(10));
		$element->setValue('12345');
		$this->assertFalse($element->isValid());
		$element->setValue('1234567890');
		$this->assertTrue($element->isValid());
	}

	public function testAlphanumericValidator()
	{
		$element = $this->getTestTextElement();

		//Validate Alphanumeric
		$element->addValidator(AlnumValidator::create());
		$element->setValue("This is a sentence.");
		$this->assertFalse($element->isValid());
		$element->setValue('MyUsername1');
		$this->assertTrue($element->isValid());
	}

	public function testDateValidator()
	{
		$element = $this->getTestTextElement();

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

	public function testEmailValidator()
	{
		$element = $this->getTestTextElement();

		//Validate Email Address
		$element->addValidator(EmailValidator::create());
		$element->setValue("notemyemail");
		$this->assertFalse($element->isValid());
		$element->setValue('Thisemail@works.com');
		$this->assertTrue($element->isValid());
	}
}
