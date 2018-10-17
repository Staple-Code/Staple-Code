<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 3:12 PM
 */

namespace Staple\Tests;

use Staple\Validate\InArrayValidator;
use PHPUnit\Framework\TestCase;

class InArrayValidatorTest extends TestCase
{
    private function getValidationObject($userMessage = null, array $array = [])
    {
        return new InArrayValidator($userMessage, $array);
    }

    public function test__construct()
    {
        $validator1 = new InArrayValidator();
        $validator2 = new InArrayValidator('My error message');
        $validator3 = new InArrayValidator('My error message', [1, 2, 3]);

        $this->assertInstanceOf('\Staple\Validate\InArrayValidator', $validator1);
        $this->assertInstanceOf('\Staple\Validate\InArrayValidator', $validator2);
        $this->assertInstanceOf('\Staple\Validate\InArrayValidator', $validator3);
    }

    public function testAddValue()
    {
        $validator = $this->getValidationObject(null, [1, 2, 3, 4]);

        $validator->addValue(5);

        $this->assertTrue($validator->check(5));
    }

    public function testCheck()
    {
        $validator = $this->getValidationObject(null, [1, 2, 3, 4]);

        $this->assertFalse($validator->check(5));
        $this->assertTrue($validator->check(1));
        $this->assertTrue($validator->check(3));
        $this->assertFalse($validator->check(13324));
    }

    public function testCreate()
    {
        $validator1 = InArrayValidator::create(null, [1, 2, 3, 4]);
        $validator2 = InArrayValidator::create('Test Message', [1, 2, 3, 4]);

        $this->assertInstanceOf('\Staple\Validate\InArrayValidator', $validator1);
        $this->assertTrue($validator1->check(4));
        $this->assertInstanceOf('\Staple\Validate\InArrayValidator', $validator2);
        $this->assertFalse($validator2->check(293));
        $this->assertEquals(['Test Message'], $validator2->getErrors());
    }
}
