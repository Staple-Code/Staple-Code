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

use PHPUnit\Framework\TestCase;
use Staple\Form\TextareaElement;
use Staple\Form\TextElement;
use Staple\Form\Validate\DependentFieldValidator;

class DependentFieldValidatorTest extends TestCase
{

	/*
	 * ***** Object instantiation Tests *****
	 */

	/**
	 * Test instantiation of DependentFieldValidator
	 */
	public function testCreate()
	{
		// Test field for instantiation
		$testfield = TextElement::create('TestField');

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', new DependentFieldValidator($testfield));
	}

	/**
	 * Test static method equal for instantiation of DependentFieldValidator
	 */
	public function testEqualCreate()
	{
		// Test field for instantiation
		$testfield = TextElement::create('TestField');

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::equal($testfield));
	}

	/**
	 * Test static method lessThan for instantiation of DependentFieldValidator
	 */
	public function testLessThanCreate()
	{
		// Test field for instantiation
		$testfield = TextElement::create('TestField');

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::lessThan($testfield));
	}

	/**
	 * Test static method greaterThan for instantiation of DependentFieldValidator
	 */
	public function testGreaterThanCreate()
	{
		// Test field for instantiation
		$testfield = TextElement::create('TestField');

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::greaterThan($testfield));
	}

	/**
	 * Test static method lessThanEqualTo for instantiation of DependentFieldValidator
	 */
	public function testLessThanEqualToCreate()
	{
		// Test field for instantiation
		$testfield = TextElement::create('TestField');

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::lessThanEqualTo($testfield));
	}

	/**
	 * Test static method greaterThanEqualTo for instantiation of DependentFieldValidator
	 */
	public function testGreaterThanEqualToCreate()
	{
		// Test field for instantiation
		$testfield = TextElement::create('TestField');

		$this->assertInstanceOf('Staple\Form\Validate\DependentFieldValidator', DependentFieldValidator::greaterThanEqualTo($testfield));
	}



	/**
	 * ***** Getters and Setters Tests *****
	 */

	/*
	 * Field Getter and Setter Test
	 */
	public function testFieldGetterAndSetter()
	{
		// Instantiate DependentFieldValidator with test field.
		$testfield = TextElement::create('TestField');
		$dependentFieldValidator = new DependentFieldValidator($testfield);

		// Create different field to set Field to using Setter
		$testarea = TextareaElement::create('TestArea');

		// Test Field Getter and Setter
		$dependentFieldValidator->setField($testarea);
		$this->assertInstanceOf('Staple\Form\TextareaElement', $dependentFieldValidator->getField());
	}

	/*
	 * FieldValue Getter and Setter Test
	 */
	public function testFieldValueGetterAndSetter()
	{
		// Instantiate DependentFieldValidator with test field.
		$testfield = TextElement::create('TestField');
		$dependentFieldValidator = new DependentFieldValidator($testfield);

		//Test Fieldvalue Getter
		$testfield->setValue(1);
		$dependentFieldValidator->setField($testfield);
		$this->assertEquals(1, $dependentFieldValidator->getFieldValue());
		$this->assertNotEquals(2, $dependentFieldValidator->getFieldValue());

	}

	/*
	 * Operation Getter and Setter Test
	 */
	public function testOperationGetterAndSetter()
	{
		// Instantiate DependentFieldValidator with test field.
		$testfield = TextElement::create('TestField');
		$dependentFieldValidator = new DependentFieldValidator($testfield);

		// Test Operation Getter and Setter
		$dependentFieldValidator->setOperation($dependentFieldValidator::GREATERTHAN);
		$this->assertEquals($dependentFieldValidator::GREATERTHAN, $dependentFieldValidator->getOperation());
	}

	/*
	 * ***** Comparison Operations Tests *****
	 *
	 * ** Numerical Values **
	 */

	/*
	 * Test equal to functionality
	 */
	public function testEqualFunctionalityEqualValues()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(2);

		$dependentFieldValidator = DependentFieldValidator::equal($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testEqualFunctionalityUnEqualValues()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(3);

		$dependentFieldValidator = DependentFieldValidator::equal($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
	 * Test less than functionality
	 */
	public function testLessThanFunctionalityLessThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(1);

		$dependentFieldValidator = DependentFieldValidator::lessThan($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testLessThanFunctionalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(2);

		$dependentFieldValidator = DependentFieldValidator::lessThan($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testLessThanFunctionalityGreaterThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(3);

		$dependentFieldValidator = DependentFieldValidator::lessThan($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
	 * Test greater than functionality
	 */
	public function testGreaterThanFunctionalityGreaterThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(3);

		$dependentFieldValidator = DependentFieldValidator::greaterThan($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testGreaterThanFunctionalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(2);

		$dependentFieldValidator = DependentFieldValidator::greaterThan($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));;
	}

	public function testGreaterThanFunctionalityLessThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(1);

		$dependentFieldValidator = DependentFieldValidator::greaterThan($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
	 * Test less than equal to functionality
	 */
	public function testLessThanEqualToFunctionalityLessThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(1);

		$dependentFieldValidator = DependentFieldValidator::lessThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testLessThanEqualToFunctionalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(2);

		$dependentFieldValidator = DependentFieldValidator::lessThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testLessThanEqualToFunctionalityGreaterThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(3);

		$dependentFieldValidator = DependentFieldValidator::lessThanEqualTo($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
	 * Test greater than equal to functionality
	 */
	public function testGreaterThanEqualToFunctionalityGreaterThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(3);

		$dependentFieldValidator = DependentFieldValidator::greaterThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testGreaterThanEqualToFunctionalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(2);

		$dependentFieldValidator = DependentFieldValidator::greaterThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testGreaterThanEqualToFunctionalityLessThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue(2);
		$testfield2->setValue(1);

		$dependentFieldValidator = DependentFieldValidator::greaterThanEqualTo($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
 	 * ** Non Numerical Value Tests **
	 */

	/*
	 * Test equal functionality
	 */
	public function testEqualToNonNumericFuncationalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('test');

		$dependentFieldValidator = DependentFieldValidator::equal($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testEqualToNonNumericFuncationalityUnEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tester');

		$dependentFieldValidator = DependentFieldValidator::equal($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
	 * Test less than functionality
	 */
	public function testLessThanNonNumericFunctionalityLessThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tes');

		$dependentFieldValidator = DependentFieldValidator::lessThan($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testLessThanNonNumericFunctionalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('test');

		$dependentFieldValidator = DependentFieldValidator::lessThan($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testLessThanNonNumericFunctionalityGreaterThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tester');

		$dependentFieldValidator = DependentFieldValidator::lessThan($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
	 * Test greater than functionality
	 */
	public function testGreaterThanNonNumericFunctionalityGreaterThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tester');

		$dependentFieldValidator = DependentFieldValidator::greaterThan($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testGreaterThanNonNumericFunctionalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('test');

		$dependentFieldValidator = DependentFieldValidator::greaterThan($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testGreaterThanNonNumericFunctionalityLessThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tes');

		$dependentFieldValidator = DependentFieldValidator::greaterThan($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
	 * Test less than equal to functionality
	 */
	public function testLessThanEqualToNonNumericFunctionalityLessThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tes');

		$dependentFieldValidator = DependentFieldValidator::lessThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testLessThanEqualToNonNumericFunctionalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('test');

		$dependentFieldValidator = DependentFieldValidator::lessThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testLessThanEqualToNonNumericFunctionalityGreaterThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tester');

		$dependentFieldValidator = DependentFieldValidator::lessThanEqualTo($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}

	/*
	 * Test greater than equal to functionality
	 */
	public function testGreaterThanEqualToNonNumericFunctionalityGreaterThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tester');

		$dependentFieldValidator = DependentFieldValidator::greaterThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testGreaterThanEqualToNonNumericFunctionalityEqualValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('test');

		$dependentFieldValidator = DependentFieldValidator::greaterThanEqualTo($testfield);
		$this->assertTrue($dependentFieldValidator->check($testfield2->getValue()));
	}

	public function testGreaterThanEqualToNonNumericFunctionalityLessThanValue()
	{
		$testfield = TextElement::create('TestField');
		$testfield2 = TextElement::create('TestField2');

		$testfield->setValue('test');
		$testfield2->setValue('tes');

		$dependentFieldValidator = DependentFieldValidator::greaterThanEqualTo($testfield);
		$this->assertFalse($dependentFieldValidator->check($testfield2->getValue()));
	}
}
