<?php
/**
 * Unit Tests for \Staple\Form\Validate\DependentFieldValidator object
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

use Staple\Form\TextareaElement;
use Staple\Form\TextElement;
use Staple\Form\Validate\DependentFieldValidator;

class DependentFieldValidatorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instantiation of DependentFieldValidator
	 */
	public function testCreate()
	{
		// Test field for instantiation
		$testfield = TextElement::create('TestField');

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', new DependentFieldValidator($testfield));

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::equal($testfield));

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::lessThan($testfield));

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::greaterThan($testfield));

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::lessThanEqualTo($testfield));

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::greaterThanEqualTo($testfield));
	}

	/**
	 * Test DependentFieldValidator Getters and Setters
	 */
	public function testGettersAndSetters()
	{
		// Instantiate DependentFieldValidator with test field.
		$testfield = TextElement::create('TestField');
		$dependentFieldValidator = new DependentFieldValidator($testfield);

		// Create different field to set Field value to using Setter
		$testarea = TextareaElement::create('TestArea');

		// Test Field Getter and Setter
		$dependentFieldValidator->setField($testarea);
		$this->assertInstanceOf('Staple\Form\TextareaElement', $dependentFieldValidator->getField());

		//Test Fieldvalue Getter
		$testfield->setValue(1);
		$dependentFieldValidator->setField($testfield);
		$this->assertEquals(1, $dependentFieldValidator->getFieldvalue());
		$this->assertNotEquals(2, $dependentFieldValidator->getFieldvalue());

		// Test Operation Getter and Setter
		$dependentFieldValidator->setOperation($dependentFieldValidator::GREATERTHAN);
		$this->assertEquals($dependentFieldValidator::GREATERTHAN, $dependentFieldValidator->getOperation());
	}

	public function testOperationComparisonOperations()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		/*
 		 * Test functionality on Numerical Values
 		 */

		$testfield->setValue(2);

		/*
		 * Test equal functionality
		 */
		$testfield2->setValue(2);

		$dependentFieldValidator = DependentFieldValidator::equal($testfield);

		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(3);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test less than functionality
		 */
		$testfield2->setValue(1);

		$dependentFieldValidator = DependentFieldValidator::lessThan($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(2);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(3);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test greater than functionality
		 */
		$testfield2->setValue(3);

		$dependentFieldValidator = DependentFieldValidator::greaterThan($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(2);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(1);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test less than equal to functionality
		 */
		$testfield2->setValue(1);

		$dependentFieldValidator = DependentFieldValidator::lessThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(2);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(3);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test greater than equal to functionality
		 */
		$testfield2->setValue(3);

		$dependentFieldValidator = DependentFieldValidator::greaterThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(2);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue(1);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test functionality on Non-Numerical Values
		 */

		$testfield->setValue('test');

		/*
 		 * Test equal functionality
 		 */
		$testfield2->setValue('test');

		$dependentFieldValidator = DependentFieldValidator::equal($testfield);

		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('tester');
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test less than functionality
		 */
		$testfield2->setValue('tes');

		$dependentFieldValidator = DependentFieldValidator::lessThan($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('test');
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('tester');
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test greater than functionality
		 */
		$testfield2->setValue('tester');

		$dependentFieldValidator = DependentFieldValidator::greaterThan($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('test');
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('tes');
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test less than equal to functionality
		 */
		$testfield2->setValue('tes');

		$dependentFieldValidator = DependentFieldValidator::lessThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('test');
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('tester');
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

		/*
		 * Test greater than equal to functionality
		 */
		$testfield2->setValue('tester');

		$dependentFieldValidator = DependentFieldValidator::greaterThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('test');
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));

		$testfield2->setValue('tes');
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));

	}
	

}
