<?php
/**
 * Unit Tests for \Staple\Query\Insert object
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
use Staple\Query\Insert;
use Staple\Query\Select;
use Staple\Query\MockConnection;

class InsertTest extends TestCase
{
	const INSERT_IGNORE_STATEMENT = "INSERT HIGH_PRIORITY IGNORE \nINTO table (id,name,summary,rank,cat,created) \nVALUES (1,'Test','This is a test and only a test.','3.14',2,'2010-01-01 00:00:00') ";
	const INSERT_SELECT_STATEMENT = "INSERT \nINTO categories \nSELECT\nname, summary, rank, category_id AS cat, created \nFROM article_categories\nWHERE id = articles.cat";

	private function getMockConnection()
	{
		return new MockConnection('sqlite::memory:');
	}

	public function testInsertSelect()
	{
		//Setup the database
		$this->getMockConnection();

		$select = Select::create()
			->setColumns([
				'name',
				'summary',
				'rank',
				'cat'=>'category_id',
				'created'
			])
			->setTable('article_categories')
			->whereEqual('id', 'articles.cat', true);

		$insertSelect = Insert::create()
			->setTable('categories')
			->setData($select);

		$this->assertEquals(self::INSERT_SELECT_STATEMENT, (string)$insertSelect);
	}

	public function testInsertIgnore()
	{
		//Create the Query
		$insert = Insert::create()
			->setTable('table')
			->setPriority(Insert::HIGH)
			->setIgnore(true)
			->addData(['id'	=>	1,
					   'name'		=>	'Test',
					   'summary'	=>	'This is a test and only a test.',
					   'rank'		=>	3.14,
					   'cat'		=>	2,
					   'created' 	=>	new \DateTime('2010-01-01 00:00:00')
			]);

		$this->assertEquals(self::INSERT_IGNORE_STATEMENT,(string)$insert);
	}
}
