<?php
/**
 * Unit Tests for \Staple\Query\Select object
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


class SelectTest extends \PHPUnit_Framework_TestCase
{
	//@todo refactor this
	public function testQuery()
	{
		$this->markTestIncomplete();

		//Show me errors for dev purposes.
		ini_set('display_errors', 1);

		echo '<h1>Query Test</h1>';

		//Setup the database
		//@todo use a mock here


		$p = new Select();
		$p->addColumn('name')
			->setTable('table')
			->whereEqual('id', 'articles.cat', true);

		//Create the Query
		$q = new Staple_Query_Select();
		$q
			->setTable('articles')
			->whereIn('articles.id', array(1,2,3,4,5))
			->orderBy(array('articles.name','summary'))
			->limit(3,1)
			->innerJoin('article_categories','articles.cat=article_categories.id');

		echo "<p><h3>Query:</h3> ".$q->build()."</p>";

		//Execute the Query
		$result = $q->execute();

		echo '<h3>Results:</h3><table border="1" cellspacing="0" cellpadding="5">';
		$first = true;
		if($result instanceof mysqli_result)
		{
			while($myrow = $result->fetch_assoc())
			{
				if($first)
				{
					echo '<tr>';
					foreach($myrow as $name=>$value)
					{
						echo "<th>$name</th>";
					}
					echo '<tr>';
				}
				echo '<tr>';
				foreach($myrow as $value)
				{
					echo "<td>$value</td>";
				}
				$first = false;
				echo '</tr>';
			}
		}
		echo "</table><h3>Object Dump:</h3><h4>Query:</h4>";

		Staple_Dev::Dump($q);
	}
}
