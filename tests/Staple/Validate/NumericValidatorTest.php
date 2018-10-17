<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 4:28 PM
 */

namespace Staple\Tests;

use Staple\Validate\NumericValidator;
use PHPUnit\Framework\TestCase;

class NumericValidatorTest extends TestCase
{

    public function testCheck()
    {
        $validator = new NumericValidator();
        $this->assertTrue($validator->check('1234'));
        $this->assertTrue($validator->check(7382));
        $this->assertFalse($validator->check('0.232'));
        $this->assertFalse($validator->check(5.6));
        $this->assertFalse($validator->check('%20'));
    }
}
