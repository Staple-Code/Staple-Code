<?php
/**
 * Created by PhpStorm.
 * User: Hans
 * Date: 9/17/2016
 * Time: 3:39 PM
 */

namespace Staple\Tests;


use PHPUnit\Framework\TestCase;
use Staple\Form\Validate\CallbackValidator;

class CallbackValidatorTest extends TestCase
{
	/**
	 * @test
	 * 
	 * Verifies CallbackValidator instanciates
	 */
	public function testCreate()
	{
		$CallbackValidator = new CallbackValidator(function(){
			return TRUE;
		});
		
		$this->assertInstanceOf('Staple\Form\Validate\CallbackValidator', $CallbackValidator);
	}

	/**
	 * @test
	 *
	 * Tests functionality of check method
	 */
	public function testCheck()
	{
		$CallbackValidator = new CallbackValidator(function($data){
			return $data == TRUE ? TRUE : FALSE;
		});

		$this->assertTrue($CallbackValidator->check(TRUE));

		$this->assertFalse($CallbackValidator->check(FALSE));
	}
}

