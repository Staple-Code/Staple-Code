<?php
/**
 * Created by PhpStorm.
 * User: conta
 * Date: 10/11/2018
 * Time: 2:53 PM
 */

namespace Staple\Tests;

use Staple\Validate\FloatValidator;
use PHPUnit\Framework\TestCase;

class FloatValidatorTest extends TestCase
{

    public function testCheck()
    {
        $validator = new FloatValidator();

        $this->assertTrue($validator->check(4.5));
        $this->assertTrue($validator->check(7));
        $this->assertFalse($validator->check('Test'));
    }
}
