<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 10/19/2016
 * Time: 9:13 AM
 */

namespace Staple\Tests;


use PHPUnit\Framework\TestCase;
use Staple\Validate\DateValidator;

class DateValidatorTest extends TestCase
{
	private function getDateValidator()
	{
		return new DateValidator();
	}

	/**
	 *
	 * @test
	 */
	public function testDateValidation()
	{
		$validator = $this->getDateValidator();

		//True checks
		$this->assertTrue($validator->check('11-23-1956'));
		$this->assertTrue($validator->check('11.23-1956'));
		$this->assertTrue($validator->check('11/23/1956'));
		$this->assertTrue($validator->check('02-3-2011'));
		$this->assertTrue($validator->check('1-1-1900'));
		$this->assertTrue($validator->check('1956-11-23'));
		$this->assertTrue($validator->check('1956.11.23'));
		$this->assertTrue($validator->check('1956/10/23'));
		$this->assertTrue($validator->check('2011-02-3'));
		$this->assertTrue($validator->check('1900/1/1'));

		//Failure Checks
		$this->assertFalse($validator->check('02-30-2000'));		//Invalid Day Value for February
		$this->assertFalse($validator->check('11.23-19566'));		//Invalid Year Value
		$this->assertFalse($validator->check('13/23/1956'));		//Invalid Month Value
		$this->assertFalse($validator->check('02-33-2011'));		//Invalid Day Value
		$this->assertFalse($validator->check('1?1-1900'));			//Incorrect Separator
		$this->assertFalse($validator->check('1956-1A-23'));		//Embedded characters
		$this->assertFalse($validator->check('19l6.11.23'));		//Embedded characters
		$this->assertFalse($validator->check('2000-02-30'));		//Invalid Day Value for February
	}

	public function testGetMatches()
    {
        $validator = $this->getDateValidator();

        $this->assertTrue($validator->check('1956/10/23'));
        $this->assertEquals([
            0 => '1956/10/23',
            1 => '1956',
            2 => '19',
            3 => '10/23',
            4 => '10/23',
            5 => '10',
            6 => '23',
        ], $validator->getMatches());
    }
}
