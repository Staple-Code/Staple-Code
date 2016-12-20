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

use Staple\Form\CheckboxElement;
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;
use PHPUnit\Framework\TestCase;

class CheckboxElementTest extends TestCase
{
	const STANDARD_BUILD = "<div class=\"form_element element_checkbox\" id=\"TestCheckboxElement_element\">\n\t<input type=\"checkbox\" id=\"TestCheckboxElement\" name=\"TestCheckboxElement\" value=\"1\" class=\"form_element element_checkbox\">\n\t<label for=\"TestCheckboxElement\" class=\"form_element element_checkbox\">My Test Checkbox Element</label>\n</div>";
	const FOUNDATION_BUILD = "<div class=\"row\">\n\t<div class=\"small-12 columns\">\n\t\t<label for=\"TestCheckboxElement\" class=\"row\">My Test Checkbox Element</label>\n\t</div>\n\t<div class=\"small-12 columns\">\n\t\t<input type=\"checkbox\" id=\"TestCheckboxElement\" name=\"TestCheckboxElement\" value=\"1\" class=\"row\">\n\t</div>\n</div>\n";
	const BOOTSTRAP_BUILD = "<div class=\"checkbox\">\n\t<label class=\"control-label\" for=\"TestCheckboxElement\">\n\t<input type=\"checkbox\" id=\"TestCheckboxElement\" name=\"TestCheckboxElement\" value=\"1\">\n\tMy Test Checkbox Element</label>\n</div>\n";
	/**
	 * @return CheckboxElement
	 */
	private function getTestCheckboxElement()
	{
		return CheckboxElement::create('TestCheckboxElement','My Test Checkbox Element');
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
		$element = $this->getTestCheckboxElement();

		$buf = $element->build();

		$this->assertEquals(self::STANDARD_BUILD,$buf);
	}

	/**
	 * Test Foundation Build for this field.
	 * @test
	 */
	public function testFoundationBuild()
	{
		$element = $this->getTestCheckboxElement();
		$element->setElementViewAdapter($this->getFoundationViewAdapter());

		$buf = $element->build();

		$this->assertEquals(self::FOUNDATION_BUILD,$buf);
	}

	/**
	 * Test Bootstrap Build for this field
	 * @test
	 */
	public function testBootstrapBuild()
	{
		$element = $this->getTestCheckboxElement();
		$element->setElementViewAdapter($this->getBootstrapViewAdapter());

		$buf = $element->build();

		$this->assertEquals(self::BOOTSTRAP_BUILD,$buf);
	}

	/**
	 * Test that we can set and retrieve values from the object
	 * @test
	 * @throws \Exception
	 */
	public function testValueSetAndRetrieve()
	{
		$element = $this->getTestCheckboxElement();

		$this->assertFalse($element->isChecked());
		$element->setChecked(true);
		$this->assertEquals(1, $element->getValue());
		$this->assertTrue($element->isChecked());
		$element->setChecked(false);
		$this->assertFalse($element->isChecked());
		$this->assertNotEquals(1, $element->getValue());
	}

	/**
	 * Test base validator to ensure that it works properly on this field.
	 * @test
	 */
	public function testBaseValidator()
	{
		$element = $this->getTestCheckboxElement();

		//An element with no validators should individually assert true when asked if valid, no content and not required.
		$this->assertTrue($element->isValid());
		$element->setRequired(true);
		$this->assertFalse($element->isValid());
		$element->setChecked();
		$this->assertTrue($element->isValid());
		$element->setRequired(false);
	}
}
