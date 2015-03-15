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
	 * A sentence to test with.
	 * @var string
	 */
	protected $sentence = 'The quick brown fox jumped over the lazy dog.';

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

		//Test caching
		$this->assertEquals('cabins', Utility::pluralize('cabin'));

		//Test Unchanged
		$this->assertEquals('cars', Utility::pluralize('cars'));
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

		//Test caching
		$this->assertEquals('bed', Utility::singularize('beds'));

		//Test Unchanged
		$this->assertEquals('airplane', Utility::singularize('airplane'));
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

	/**
	 * Test the firstWord method
	 * @test
	 */
	public function testFirstWord()
	{
		$this->assertEquals('The',Utility::firstWord($this->sentence));
	}

	/**
	 * Test the wordLimit method
	 * @test
	 */
	public function testWordLimit()
	{
		$this->assertEquals('The quick brown fox',Utility::wordLimit($this->sentence,4));
		$this->assertEquals('The quick brown fox jumped over the lazy dog.',Utility::wordLimit($this->sentence,27));
	}

	/**
	 * Test the wordCount method
	 * @test
	 */
	public function testWordCount()
	{
		$this->assertEquals(9,Utility::wordCount($this->sentence));
	}

	/**
	 * Test the arraySearch method
	 * @test
	 */
	public function testArraySearch()
	{
		$array = [
			1 => [11,22,33,44,55,66,77],
			2 => [2,4,6,8,10],
			3 => [3,9,27,81,243]
		];

		$this->assertEquals([3,3],Utility::arraySearch(81,$array));
		$this->assertEquals([2],Utility::arraySearch([2,4,6,8,10],$array));
		$this->assertFalse(Utility::arraySearch(26,$array));
	}

	public function testStatesArray()
	{
		$statesShort = Utility::statesArray(Utility::STATES_SHORT);
		$statesLong = Utility::statesArray(Utility::STATES_LONG);
		$statesDefault = Utility::statesArray(Utility::STATES_BOTH);

		$this->assertArrayHasKey(10,$statesShort);
		$this->assertEquals('GA',$statesShort[10]);

		$this->assertArrayHasKey(50,$statesLong);
		$this->assertEquals('Nebraska',$statesLong[27]);

		$this->assertArrayHasKey('PA',$statesDefault);
		$this->assertEquals('Nebraska',$statesDefault['NE']);
		$this->assertEquals('Alaska',$statesDefault['AK']);
	}
}
