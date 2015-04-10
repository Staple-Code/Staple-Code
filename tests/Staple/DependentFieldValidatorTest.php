<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 4/9/2015
 * Time: 8:14 PM
 */

namespace Staple\Tests;

use Staple\Form\FieldElement;
use Staple\Form\Validate\DependentFieldValidator;

class dummyField extends FieldElement
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
	private function makeDummyField($name)
	{
		return new dummyField($name);
	}

	public function testDependentFieldsAreEqual()
	{
		$field1 = $this->makeDummyField('field1');
		$field2 = $this->makeDummyField('field2');

		$validator = new DependentFieldValidator($field2);

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
}
