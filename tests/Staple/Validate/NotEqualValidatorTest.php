<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 4:28 PM
 */

namespace Staple\Tests;

use Staple\Validate\NotEqualValidator;
use PHPUnit\Framework\TestCase;

class NotEqualValidatorTest extends TestCase
{

    public function testCheck()
    {
        $validator1 = new NotEqualValidator("5");
        $validator2 = new NotEqualValidator(4, true);

        $this->assertTrue($validator1->check("12"));
        $this->assertFalse($validator1->check("5"));
        $this->assertFalse($validator1->check(5));
        $this->assertTrue($validator2->check("4"));
        $this->assertFalse($validator2->check(4));
    }

    public function test__construct()
    {
        $validator1 = new NotEqualValidator("5");
        $validator2 = new NotEqualValidator("5", true);
        $validator3 = new NotEqualValidator(5, true, "Message");

        $this->assertInstanceOf('\Staple\Validate\NotEqualValidator', $validator1);
        $this->assertInstanceOf('\Staple\Validate\NotEqualValidator', $validator2);
        $this->assertInstanceOf('\Staple\Validate\NotEqualValidator', $validator3);
    }
}
