<?php
/**
 * MockMailAdapter for testing PHP mail() function email adapter.
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

use Staple\Email\MailAdapter;

class MockMailAdapter extends MailAdapter
{
	/**
	 * @param $to
	 * @param $subject
	 * @param $body
	 * @param $headers
	 * @return \stdClass
	 */
	protected function performSend($to, $subject, $body, $headers)
	{
		$obj = new \stdClass();
		$obj->to = $to;
		$obj->subject = $subject;
		$obj->body = $body;
		$obj->headers = $headers;
		return $obj;
	}
}