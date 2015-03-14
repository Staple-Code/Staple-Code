<?php
/**
 * Unit Tests for \Staple\Utility object
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
 */

namespace Staple\Tests;


use Staple\Utility;

class UtilityTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test that the pluralization function works properly
	 * @test
	 */
	public function testPluralization()
	{
		$arraySingularWords = array('person','bed','cabin','man','airplane','quiz','goose','alias','matrix','hypermedia','news');
		$arrayPluralWords = array('people','beds','cabins','men','airplanes','quizzes','geese','aliases','matrices','hypermedia','news');

		foreach($arraySingularWords as $key=>$word)
		{
			$this->assertEquals($arrayPluralWords[$key], Utility::pluralize($word));
		}
	}

	/**
	 * Test that the singularization function works properly
	 * @test
	 */
	public function testSingularization()
	{
		$arraySingularWords = array('person','bed','cabin','man','airplane','quiz','goose','alias','matrix','hypermedia','news');
		$arrayPluralWords = array('people','beds','cabins','men','airplanes','quizzes','geese','aliases','matrices','hypermedia','news');

		foreach($arrayPluralWords as $key=>$word)
		{
			$this->assertEquals($arraySingularWords[$key], Utility::singularize($word));
		}
	}

	/**
	 * Test the snakeCase function....or is that snake_case?
	 * @test
	 */
	public function testSnakeCase()
	{
		$camelCase = array('myWord','SomethingNew','MyNewAwesomeManicMethod','snakesOnAPlane');
		$snake_case = array('my_word','something_new','my_new_awesome_manic_method','snakes_on_a_plane');

		foreach($camelCase as $key=>$word)
		{
			$this->assertEquals($snake_case[$key], Utility::snakeCase($word));
		}
	}
}
