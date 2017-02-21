<?php
/**
 * Unit Tests for \Staple\Link object
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
use Staple\Link;

class LinkTest extends TestCase
{
	public function testLinkCreation()
	{
		$link1 = Link::get(array('downloadTestTest'));
		$link2 = Link::get(array('links'));
		$link3 = Link::get(array('testLinks','testLinks'));
		$link4 = new Link(array('links','to','myPage'));

		$this->assertEquals($link1, '/download-test-test');
		$this->assertEquals($link2, '/links');
		$this->assertEquals($link3, '/test-links/test-links');
		$this->assertEquals((string)$link4, '/links/to/myPage');
	}
}
