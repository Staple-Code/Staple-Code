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
use Staple\Form\SubmitElement;
use Staple\Form\Validate\EqualValidator;
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;

class SubmitElementTest extends TestCase
{
	const STANDARD_BUILD = "<div class=\"form_element element_submit\" id=\"SubmitElement_element\">\n\t<label for=\"SubmitElement\" class=\"form_element element_submit\"></label>\n\t<input type=\"submit\" id=\"SubmitElement\" name=\"SubmitElement\" value=\"Submit Form\" class=\"form_element element_submit\">\n</div>\n";
	const FOUNDATION_BUILD = "<div class=\"row\">\n\t<div class=\"small-12 columns\">\n\t<input type=\"submit\" id=\"SubmitElement\" name=\"SubmitElement\" value=\"Submit Form\" class=\"button\">\n\t</div>\n</div>\n";
	const BOOTSTRAP_BUILD = "<div class=\"form-group\">\n\t<input type=\"submit\" id=\"SubmitElement\" name=\"SubmitElement\" value=\"Submit Form\" class=\"btn\">\n</div>\n";
	/**
	 * @return SubmitElement
	 */
	private function getTestElement()
	{
		return SubmitElement::create('SubmitElement','Submit Form');
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
		$element = $this->getTestElement();

		$buf =  $element->build();

		$this->assertEquals(self::STANDARD_BUILD,$buf);
	}

	/**
	 * Test Foundation Build for this field.
	 * @test
	 */
	public function testFoundationBuild()
	{
		$element = $this->getTestElement();
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
		$element = $this->getTestElement();
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
		$element = $this->getTestElement();

		$element->setValue('TestValue');

		$this->assertEquals('TestValue',$element->getValue());
	}

	public function testThrowOnValidatorAdd()
	{
		$element = $this->getTestElement();

		try
		{
			$element->addValidator(EqualValidator::Create('Submit'));
			$this->assertEquals(1,0);
		}
		catch (\Exception $e)
		{
			$this->assertInstanceOf('\Staple\Exception\FormBuildException', $e);
		}
	}
}
