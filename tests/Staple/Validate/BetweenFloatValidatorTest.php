<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 3:55 PM
 */

namespace Staple\Tests;

use Staple\Validate\BetweenFloatValidator;
use PHPUnit\Framework\TestCase;

class BetweenFloatValidatorTest extends TestCase
{
    private function getValidatorObject($min, $max, $userMessage = null)
    {
        return new BetweenFloatValidator($min, $max, $userMessage);
    }

    public function testSetMin()
    {
        $validator = $this->getValidatorObject(1.4, 5.5);
        $validator->setMin(0.3);
        $this->assertEquals(0.3, $validator->getMin());
    }

    public function testSetMax()
    {
        $validator = $this->getValidatorObject(1.4, 5.5);
        $validator->setMax(12.23);
        $this->assertEquals(12.23, $validator->getMax());
    }

    public function testCheck()
    {
        $validator = $this->getValidatorObject(1.4, 5.5);

        $this->assertTrue($validator->check(5));
        $this->assertTrue($validator->check(1.4));
        $this->assertTrue($validator->check(5.5));
        $this->assertFalse($validator->check(6.23));
        $this->assertFalse($validator->check(-12.2));
        $this->assertFalse($validator->check(1.3999));
    }

}
