<?php
/**
 * Test Cases for SelectElement Class
 *
 * @author Ironpilot
 * @copyright Copyright (c) 2011, STAPLE CODE
 *
 * This file is part of the STAPLE Framework.
 *
 * The STAPLE Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * The STAPLE Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the STAPLE Framework.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Staple\Tests;

use Staple\Form\Filter\IntegerFilter;
use PHPUnit\Framework\TestCase;

class IntegerFilterTest extends TestCase
{
	/**
	 * @return IntegerFilter
	 */
	private function getTestIntegerFilter()
	{
		return new IntegerFilter();
	}

	/**
	 * Standard Output Build Test
	 * @test
	 */
	public function testFilter()
	{
		$filter = $this->getTestIntegerFilter();

		$test1 = $filter->filter('test123');
		$test2 = $filter->filter('123');
		$test3 = $filter->filter('47ronin');

		$this->assertEquals(0, $test1);
		$this->assertEquals(123, $test2);
		$this->assertEquals(47, $test3);
		$this->assertEquals('integer', $filter->getName());
	}
}
