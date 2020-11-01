<?php
/**
 * Test Cases for Pager Class
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


use PHPUnit\Framework\TestCase;
use Staple\Pager;

class PagerTest extends TestCase
{
	private function getTestObject($itemsPerPage = NULL, $total = NULL, $currentPage = NULL, $pageBuffer = NULL)
	{
		return new Pager($itemsPerPage, $total, $currentPage, $pageBuffer);
	}

	public function testObjectCreation()
	{
		$pagerBlank = $this->getTestObject();
		$pager1 = $this->getTestObject(15);
		$pager2 = $this->getTestObject(NULL, 45);
		$pager3 = $this->getTestObject(NULL, NULL, 5);
		$pager4 = $this->getTestObject(NULL, NULL, NULL, 4);

		//Assert Blank
		$this->assertInstanceOf('Staple\Pager',$pagerBlank);
		$this->assertEquals(10, $pagerBlank->getItemsPerPage());
		$this->assertNull($pagerBlank->getTotal());
		$this->assertEquals(1, $pagerBlank->getCurrentPage());
		$this->assertNull($pagerBlank->getPagesAfterCurrent());
		$this->assertNull($pagerBlank->getPagesBeforeCurrent());

		//Assert Pager 1
		$this->assertInstanceOf('Staple\Pager',$pager1);
		$this->assertEquals(15, $pager1->getItemsPerPage());
		$this->assertNull($pager1->getTotal());
		$this->assertEquals(1, $pager1->getCurrentPage());
		$this->assertNull($pager1->getPagesAfterCurrent());
		$this->assertNull($pager1->getPagesBeforeCurrent());

		//Assert Pager 2
		$this->assertInstanceOf('Staple\Pager',$pager2);
		$this->assertEquals(10, $pager2->getItemsPerPage());
		$this->assertEquals(45, $pager2->getTotal());
		$this->assertEquals(1, $pager2->getCurrentPage());
		$this->assertNull($pager2->getPagesAfterCurrent());
		$this->assertNull($pager2->getPagesBeforeCurrent());

		//Assert Pager 3
		$this->assertInstanceOf('Staple\Pager',$pager3);
		$this->assertEquals(10, $pager3->getItemsPerPage());
		$this->assertNull($pager3->getTotal());
		$this->assertEquals(5, $pager3->getCurrentPage());
		$this->assertNull($pager3->getPagesAfterCurrent());
		$this->assertNull($pager3->getPagesBeforeCurrent());

		//Assert Pager 4
		$this->assertInstanceOf('Staple\Pager',$pager4);
		$this->assertEquals(10, $pager4->getItemsPerPage());
		$this->assertNull($pager4->getTotal());
		$this->assertEquals(1, $pager4->getCurrentPage());
		$this->assertEquals(4, $pager4->getPagesAfterCurrent());
		$this->assertEquals(4, $pager4->getPagesBeforeCurrent());

	}

	public function testGetLastPage()
	{
		//Make the object
		$pager = $this->getTestObject();
		$pager2 = $this->getTestObject();
		$pager3 = $this->getTestObject();

		//Act
		$pager->setTotal(100);
		$pager->setItemsPerPage(5);

		$pager2->setTotal(1398);

		$pager3->setTotal(45);
		$pager3->setItemsPerPage(100);

		//Assert
		$this->assertEquals(20, $pager->getLastPage());
		$this->assertEquals(140, $pager2->getLastPage());
		$this->assertEquals(1, $pager3->getLastPage());
	}

	public function testGetPages()
	{
		//Make Objects
		$pager = $this->getTestObject();
		$pager2 = $this->getTestObject();
		$pager3 = $this->getTestObject();
		$pager4 = $this->getTestObject();

		//Act
		$pager->setTotal(100);
		$pager->setItemsPerPage(10);

		$pager2->setTotal(34);
		$pager2->setItemsPerPage(30);

		$pager3->setTotal(20);
		$pager3->setItemsPerPage(50);

		$pager4->setTotal(50);
		$pager4->setItemsPerPage(2);
		$pager4->setPagesAfterCurrent(3);
		$pager4->setPagesBeforeCurrent(3);
		$pager4->setPage(10);

		//Assert
		$this->assertCount(10, $pager->getPages());
		$this->assertEquals([1,2,3,4,5,6,7,8,9,10],$pager->getPages());
		$this->assertCount(2, $pager2->getPages());
		$this->assertEquals([1,2],$pager2->getPages());
		$this->assertCount(1, $pager3->getPages());
		$this->assertEquals([1],$pager3->getPages());

		$this->assertCount(7, $pager4->getPages());
		$this->assertEquals([7,8,9,10,11,12,13], $pager4->getPages());
	}
}
