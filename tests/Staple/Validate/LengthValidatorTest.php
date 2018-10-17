<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 4:01 PM
 */

namespace Staple\Tests;

use Staple\Validate\LengthValidator;
use PHPUnit\Framework\TestCase;

class LengthValidatorTest extends TestCase
{
    private function getValidateObject($min, $max, $userMessage= null)
    {
        return new LengthValidator($min, $max, $userMessage);
    }

    public function testCheck()
    {
        $validator1 = $this->getValidateObject(4, 10);
        $validator2 = $this->getValidateObject(20, 5, 'My Error Message');

        $this->assertTrue($validator1->check("Test"));
        $this->assertFalse($validator1->check("My test string"));
        $this->assertFalse($validator1->check("An Airplane"));
        $this->assertEquals([LengthValidator::MAX_LENGTH_ERROR], $validator1->getErrors());

        $validator1->clearErrors();

        $this->assertFalse($validator1->check("ABC"));
        $this->assertEquals([LengthValidator::MIN_LENGTH_ERROR], $validator1->getErrors());
        $this->assertTrue($validator2->check("MyPassword"));
        $this->assertFalse($validator2->check("Test"));
        $this->assertEquals(['My Error Message'], $validator2->getErrors());
    }

    public function testSetMin()
    {
        $validator = $this->getValidateObject(4, 10);
        $validator->setMin(6);

        $this->assertEquals(6, $validator->getMin());
    }

    public function test__construct()
    {
        $validator1 = $this->getValidateObject(1,2);
        $validator2 = $this->getValidateObject(1,2, 'Test Message');

        $this->assertInstanceOf('\Staple\Validate\LengthValidator', $validator1);
        $this->assertInstanceOf('\Staple\Validate\LengthValidator', $validator2);
    }

    public function testSetMax()
    {
        $validator = $this->getValidateObject(4, 10);
        $validator->setMax(20);

        $this->assertEquals(20, $validator->getMax());
    }
}
