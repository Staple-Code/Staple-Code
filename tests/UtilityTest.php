<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 3/14/2015
 * Time: 11:11 AM
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
}
