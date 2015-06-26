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


use Staple\Dev;
use Staple\Query\Insert;
use Staple\Query\Select;

class InsertTest extends \PHPUnit_Framework_TestCase
{
	//@todo refactor this
	public function testInsert()
	{
		//@todo complete this test
		$this->markTestIncomplete();

		//Setup the database
		//@todo use a mock here.

		$p = new Select();
		$p->addColumn('name')
			->setTable('article_categories')
			->whereEqual('id', 'articles.cat', true);

		//Create the Query
		$q = new Insert();
		$q
			->setTable('table')
			->addData(array('id'=>1,
				'name'=>'Test',
				'quickname'=>'test',
				'summary'=>'This is a test and only a test.',
				'cat'=>2));

		echo "<p><h3>Query:</h3> ".$q->build()."</p>";

		//Execute the Query
		//$result = $db->query($q);

		echo "<h3>Object Dump:</h3><h4>Query:</h4>";

		Dev::Dump($q);
	}
}
