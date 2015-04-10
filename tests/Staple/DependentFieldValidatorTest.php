<?php
/**
 * Unit Tests for \Staple\Form\Validator\DependentFieldValidator object
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

use Staple\Form\FieldElement;
use Staple\Form\Validate\DependentFieldValidator;

class dummyDependentFieldValidator extends FieldElement
{
	/**
	 * Build the field label
	 */
	public function label()
	{
		return '';
	}

	/**
	 * Build the field itself
	 */
	public function field()
	{
		return '';
	}

	/**
	 * Build the field using a layout, or with the default build.
	 */
	public function build($fieldView = NULL)
	{
		return '';
	}

}

class DependentFieldValidatorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param $name
	 * @return dummyDependentFieldValidator
	 */
	private function makeDummyField($name)
	{
		return new dummyDependentFieldValidator($name);
	}

	private function getValidatorObject(FieldElement $field)
	{
		return new DependentFieldValidator($field);
	}

	public function testDependentFieldsAreEqual()
	{
		$field1 = $this->makeDummyField('field1');
		$field2 = $this->makeDummyField('field2');

		$validator = $this->getValidatorObject($field2);

		$field1->addValidator($validator);

		$field1->setValue('12345');
		$field2->setValue('637284');

		//Assert
		$this->assertNotEquals($field1->getValue(),$field2->getValue());
		$this->assertFalse($validator->check($field1->getValue()));

		$field2->setValue('12345');

		$this->assertEquals($field1->getValue(),$field2->getValue());
		$this->assertTrue($validator->check($field1->getValue()));

	}

	public function testGetField()
	{
		$field = $this->makeDummyField('field');
		$validator = $this->getValidatorObject($field);

		$this->assertInstanceOf('\Staple\Form\FieldElement',$validator->getField());
	}
}
