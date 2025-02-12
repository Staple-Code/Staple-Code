<?php
/**
 * Unit Tests for \Staple\Alias object
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

use PHPUnit\Framework\TestCase;
use Staple\Alias;

class AliasTest extends TestCase
{
    public function testAddAlias()
	{
        $alias = new Alias();

		//Add an alias
		$alias->addAlias('MyNewClass','\\MyNamespace\\MyNewClass');

		//Test that the array key was added
		$this->assertArrayHasKey('MyNewClass', $alias->getClassMap());

		//Test that the alias is returned when checked for.
		$this->assertEquals($alias->checkAlias('MyNewClass'),'\\MyNamespace\\MyNewClass');
	}
}
