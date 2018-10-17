<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 2:58 PM
 */

namespace Staple\Tests;

use Staple\Validate\BetweenValidator;
use PHPUnit\Framework\TestCase;

class BetweenValidatorTest extends TestCase
{

    /**
     * @param int $limit1
     * @param int $limit2
     * @param string $userMessage
     * @return BetweenValidator
     */
    private function getValidatorObject($limit1, $limit2, $userMessage = null)
    {
        return new BetweenValidator($limit1, $limit2, $userMessage);
    }

    public function test__construct()
    {
        $validator1 = $this->getValidatorObject(1, 2);
        $validator2 = $this->getValidatorObject(2, 1);
        $validator3 = $this->getValidatorObject(1, 2, 'Test Message');

        $this->assertInstanceOf('Staple\Validate\BetweenValidator', $validator1);
        $this->assertInstanceOf('Staple\Validate\BetweenValidator', $validator2);
        $this->assertInstanceOf('Staple\Validate\BetweenValidator', $validator3);
    }

    public function testSetMin()
    {
        $validator = $this->getValidatorObject(10, 20);
        $validator->setMin(5);

        $this->assertEquals(5, $validator->getMin());
    }

    public function testSetMax()
    {
        $validator = $this->getValidatorObject(10, 20);
        $validator->setMax(30);

        $this->assertEquals(30, $validator->getMax());
    }

    public function testCheck()
    {
        $validator = $this->getValidatorObject(10, 20);

        $this->assertTrue($validator->check(12));
        $this->assertTrue($validator->check(19));
        $this->assertTrue($validator->check(10));
        $this->assertTrue($validator->check(20));
        $this->assertFalse($validator->check(50));
    }
}
