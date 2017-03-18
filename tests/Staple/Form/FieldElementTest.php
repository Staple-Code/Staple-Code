<?php
/**
 * Unit Tests for \Staple\Form\FieldElement object
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

use PHPUnit\Framework\TestCase;
use Staple\Form\FieldElement;
use Staple\Form\FieldValidator;

class dummyFieldElement extends FieldElement
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

class dummyFieldElementValidator extends FieldValidator
{
	protected $value;

	public function __construct($value)
	{
		parent::__construct();
		$this->value = $value;
	}

	/**
	 *
	 * Returns a boolean true or false on success or failure of the validation check.
	 * @param mixed $data
	 * @return bool
	 */
	public function check($data)
	{
		if($data == $this->value)
			return true;
		else
		{
			$this->addError('Values are not equal.');
			return false;
		}
	}

}


class FieldElementTest extends TestCase
{
	private function getTestObject($name)
	{
		return new dummyFieldElement($name);
	}

	private function getTestValidator($value)
	{
		return new dummyFieldElementValidator($value);
	}

	public function testIsValidWithNoValidatorsAndNoContent()
	{
		$field = $this->getTestObject('field1');

		$this->assertTrue($field->isValid());
	}

	public function testIsValidWithNoValidatorsWithContentNotRequired()
	{
		$field = $this->getTestObject('field1');
		$field->setValue('value');

		$this->assertTrue($field->isValid());
	}

	public function testIsValidWithNoValidatorsWithContentRequired()
	{
		$field = $this->getTestObject('field1');
		$field->setRequired(true);

		//Should not be valid with no content
		$this->assertFalse($field->isValid());

		//Add Field Value
		$field->setValue('value');

		//Should be valid with content
		$this->assertTrue($field->isValid());
	}

	public function testIsValidWithValidatorsNoContentNotRequired()
	{
		$field = $this->getTestObject('field1');
		$field->addValidator($this->getTestValidator('value'));

		$this->assertFalse($field->isValid());
	}

	public function testIsValidWithValidatorsNoContentRequired()
	{
		$field = $this->getTestObject('field1');
		$field->addValidator($this->getTestValidator('value'))
			->setRequired(true);

		$this->assertFalse($field->isValid());
	}

	public function testIsValidWithValidatorsWithContentNotRequired()
	{
		$field = $this->getTestObject('field1');
		$field->addValidator($this->getTestValidator('bar'))
			->setValue('foo');

		$this->assertFalse($field->isValid());

		$field->setValue('bar');

		$this->assertTrue($field->isValid());
	}

	public function testIsValidWithValidatorsWithContentRequired()
	{
		$field = $this->getTestObject('field1');
		$field->addValidator($this->getTestValidator('bar'))
			->setRequired(true);

		$this->assertFalse($field->isValid());

		$field->setValue('foo');

		$this->assertFalse($field->isValid());

		$field->setValue('bar');

		$this->assertTrue($field->isValid());
	}

	/**
	 * Test that we can set and retrieve values from the object
	 * @test
	 * @throws \Exception
	 */
	public function testValueSetAndRetrieve()
	{
		$element = $this->getTestObject('MyObject');

		$element->setValue('TestValue');

		$this->assertEquals('TestValue',$element->getValue());
	}
}
