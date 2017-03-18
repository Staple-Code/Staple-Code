<?php
/**
 * Unit Tests for \Staple\Data\LinkedList object
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


use PHPUnit\Framework\TestCase;
use Staple\Data\LinkedList;

class LinkedListTest extends TestCase
{
	/**
	 * @return LinkedList
	 */
	private function getPopulatedTestObject()
	{
		$list = new LinkedList('Numbers');
		$list->add('One');
		$list->add('Two');
		$list->add('Three');
		$list->add('Four');
		$list->add('Five');
		$list->add('Six');
		$list->add('Seven');
		$list->add('Eight');
		$list->add('Nine');
		$list->rewind();

		return $list;
	}

	private function getEmptyTestObject()
	{
		return new LinkedList();
	}

	public function testLinkedListCount()
	{
		$list = $this->getPopulatedTestObject();

		$this->assertEquals($list->count(),9);
		$this->assertEquals($list->length(),9);
	}

	public function testAddNode()
	{
		$list = $this->getEmptyTestObject();

		$this->assertEquals($list->count(),0);
		$this->assertEquals($list->peakBack(),null);
		$this->assertEquals($list->peakFront(),null);

		$list->add('One');

		$this->assertEquals($list->count(),1);
		$this->assertEquals($list->peakBack(),'One');
		$this->assertEquals($list->peakFront(),'One');

		$list->add('Two');

		$this->assertEquals(2, $list->count());
		$this->assertEquals('One', $list->peakFront());
		$this->assertEquals('Two', $list->peakBack());

		$list->addFront('Zero');

		$this->assertEquals(3, $list->count());
		$this->assertEquals('Zero', $list->peakFront());
		$this->assertEquals('Two', $list->peakBack());
	}

	public function testRetrieveNode()
	{
		//Get a populated test object
		$list = $this->getPopulatedTestObject();

		//Rewind the list to the beginning.
		$list->rewind();

		//Get the first item
		$this->assertEquals('One',$list->current());
		$this->assertEquals('One',$list->peakFront());

		//Get the last item in the list
		$this->assertEquals('Nine',$list->peakBack());

		//Move to and retrieve the next item
		$list->next();
		$this->assertEquals('Two',$list->current());

		//Retrieve by array key
		$this->assertEquals('Three',$list[2]);
		$this->assertEquals('Five',$list[4]);
		$this->assertEquals('Eight',$list[7]);
	}

	public function testConvertToString()
	{
		//Setup
		$list = $this->getPopulatedTestObject();
		$expectedList = "One\nTwo\nThree\nFour\nFive\nSix\nSeven\nEight\nNine\n";
		$expectedListVerbose = "Name: Numbers \nSize: 9 \n".$expectedList;

		//Act
		$stringList = (string)$list;
		$stringListVerbose = $list->getListString(true);

		//Assert
		$this->assertEquals($expectedList,$stringList);
		$this->assertEquals($expectedListVerbose,$stringListVerbose);
	}

	public function testConvertToArray()
	{
		//Setup
		$list = $this->getPopulatedTestObject();
		$expectedList = ['One','Two','Three','Four','Five','Six','Seven','Eight','Nine'];
		$expectedListVerbose = $expectedList;
		$expectedListVerbose[-2] = 'Numbers';
		$expectedListVerbose[-1] = 9;

		//Act
		$arrayList = $list->getListArray();
		$arrayListVerbose = $list->getListArray(true);

		//Assert
		$this->assertEquals($expectedList,$arrayList);
		$this->assertEquals($expectedListVerbose,$arrayListVerbose);
	}
}
