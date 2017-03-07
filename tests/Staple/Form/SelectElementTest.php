<?php
/**
 * Test Cases for SelectElement Class
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
use Staple\Form\SelectElement;
use Staple\Form\Validate\AlnumValidator;
use Staple\Form\Validate\DateValidator;
use Staple\Form\Validate\EmailValidator;
use Staple\Form\Validate\InArrayValidator;
use Staple\Form\Validate\LengthValidator;
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;

class SelectElementTest extends TestCase
{
	const STANDARD_BUILD = "<div class=\"form_element element_select\" id=\"TestSelectElement_element\">\n\t<label for=\"TestSelectElement\" class=\"form_element element_select\">My Test Select Element</label>\n\t<select name=\"TestSelectElement\" id=\"TestSelectElement\" class=\"form_element element_select\">\n\t\t<option value=\"1\">Las Vegas</option>\n\t\t<option value=\"2\">New York</option>\n\t\t<option value=\"3\">Atlanta</option>\n\t\t<option value=\"4\">Orlando</option>\n\t</select>\n</div>\n";
	const FOUNDATION_BUILD = "<div class=\"row\">\n\t<div class=\"small-12 columns\">\n\t\t<label for=\"TestSelectElement\">My Test Select Element</label>\n\t</div>\n\t<div class=\"small-12 columns\">\n\t\t<select name=\"TestSelectElement\" id=\"TestSelectElement\">\n\t\t<option value=\"1\">Las Vegas</option>\n\t\t<option value=\"2\">New York</option>\n\t\t<option value=\"3\">Atlanta</option>\n\t\t<option value=\"4\">Orlando</option>\n\t</select>\n\t</div>\n</div>\n";
	const BOOTSTRAP_BUILD = "<div class=\"form-group\">\n\t<label class=\"control-label\" for=\"TestSelectElement\">My Test Select Element</label>\n\t<select name=\"TestSelectElement\" id=\"TestSelectElement\" class=\"form-control\">\n\t\t<option value=\"1\">Las Vegas</option>\n\t\t<option value=\"2\">New York</option>\n\t\t<option value=\"3\">Atlanta</option>\n\t\t<option value=\"4\">Orlando</option>\n\t</select>\n</div>\n";
	/**
	 * @return SelectElement
	 */
	private function getTestSelectElement()
	{
		return SelectElement::create('TestSelectElement','My Test Select Element')
				->addOptionsArray([
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
		$element = $this->getTestSelectElement();

		$buf =  $element->build();

		$this->assertEquals(self::STANDARD_BUILD,$buf);
	}

	/**
	 * Test Foundation Build for this field.
	 * @test
	 */
	public function testFoundationBuild()
	{
		$element = $this->getTestSelectElement();
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
		$element = $this->getTestSelectElement();
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
		$element = $this->getTestSelectElement();

		$element->setValue('4');

		$this->assertEquals('4',$element->getValue());
	}

	/**
	 * Test base validator to ensure that it works properly on this field.
	 * @test
	 */
	public function testBaseValidator()
	{
		$element = $this->getTestSelectElement();

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
		$element = $this->getTestSelectElement();

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
		$element = $this->getTestSelectElement();

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
		$element = $this->getTestSelectElement();

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
		$element = $this->getTestSelectElement();

		//Validate Email Address
		$element->addValidator(EmailValidator::create());
		$element->setValue("notemyemail");
		$this->assertFalse($element->isValid());
		$element->setValue('Thisemail@works.com');
		$this->assertTrue($element->isValid());
	}

	/**
	 * Test the InArrayValidator applied to the SelectElement object
	 * @test
	 */
	public function testInArrayValidator()
	{
		$element = $this->getTestSelectElement();

		$array = [
			'1'	=>	'Las Vegas',
			'2'	=>	'New York',
			'3'	=>	'Atlanta',
			'4'	=>	'Orlando'
		];

		//Validate In Array
		$element->addValidator(InArrayValidator::create(array_keys($array)));
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
