<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 4:29 PM
 */

namespace Staple\Tests;

use Staple\Validate\EqualValidator;
use PHPUnit\Framework\TestCase;

class EqualValidatorTest extends TestCase
{

    public function test__construct()
    {
        $validator1 = new EqualValidator(1);
        $validator2 = new EqualValidator(1, true);
        $validator3 = new EqualValidator(1, true, 'Strict Validation');

        $this->assertInstanceOf('\Staple\Validate\EqualValidator', $validator1);
        $this->assertInstanceOf('\Staple\Validate\EqualValidator', $validator2);
        $this->assertInstanceOf('\Staple\Validate\EqualValidator', $validator3);
    }

    public function testCheck()
    {
        $validator1 = new EqualValidator('123');
        $validator2 = new EqualValidator(5, true);

        $this->assertTrue($validator1->check('123'));
        $this->assertTrue($validator1->check(123));
        $this->assertFalse($validator1->check("20"));
        $this->assertTrue($validator2->check(5));
        $this->assertFalse($validator2->check("5"));
    }
}
