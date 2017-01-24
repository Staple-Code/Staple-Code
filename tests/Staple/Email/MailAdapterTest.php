<?php
/**
 * MailAdapter Tests
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

use Staple\Email\Email;

require_once 'MockMailAdapter.php';

class MailAdapterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Return the email object
	 * @return Email
	 */
	private function getEmailObject()
	{
		$mail = new Email();
		$mail->setEmailAdapter(new MockMailAdapter());
		return $mail;
	}

	public function testTo()
	{
		$email = $this->getEmailObject();

		$email->addTo('test@test.com')
			->addTo('email@hotmail.com');

		$this->assertEquals('test@test.comemail@hotmail.com',implode('',$email->getTo()));

		$email->setTo(['myemail@form.com']);

		$this->assertEquals('myemail@form.com',implode('',$email->getTo()));
	}

	public function testCc()
	{
		$email = $this->getEmailObject();

		$email->addCc('test@test.com')
			->addCc('email@hotmail.com');

		$this->assertEquals('test@test.comemail@hotmail.com',implode('',$email->getCc()));

		$email->setCc(['myemail@form.com']);

		$this->assertEquals('myemail@form.com',implode('',$email->getCc()));
	}

	public function testBcc()
	{
		$email = $this->getEmailObject();

		$email->addBcc('test@test.com')
			->addBcc('email@hotmail.com');

		$this->assertEquals('test@test.comemail@hotmail.com',implode('',$email->getBcc()));

		$email->setBcc(['myemail@form.com']);

		$this->assertEquals('myemail@form.com',implode('',$email->getBcc()));
	}

	public function testFrom()
	{
		$email = $this->getEmailObject();

		$email->setFrom('no-reply@test.com');

		$this->assertEquals('no-reply@test.com',$email->getFrom());
	}

	public function testSubject()
	{
		$email = $this->getEmailObject();

		$email->setSubject('My Email Subject');

		$this->assertEquals('My Email Subject',$email->getSubject());
	}

	public function testBody()
	{
		$email = $this->getEmailObject();

		$email->setBody('Email Body Contents');

		$this->assertEquals('Email Body Contents',$email->getBody());
	}

	public function testHtmlEmail()
	{

	}

	public function testEmailSend()
	{
		$email = $this->getEmailObject();

		$to = 'test@test.com';
		$from = 'no-reply@test.com';
		$subject = 'Test Subject';
		$expectedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
		$body = 'Test Body';
		$expectedHeaders = "To: $to\r\nFrom: $from\r\nMIME-Version: 1.0\r\nX-Mailer: PHP/".phpversion()."\r\nContent-type: text/html; charset=iso-8859-1\r\n";

		$email->addTo($to)
			->setFrom($from)
			->setSubject($subject)
			->setBody($body);

		$returns = $email->send();

		$this->assertEquals($to,$returns->to);
		$this->assertEquals($expectedSubject,$returns->subject);
		$this->assertEquals($body,$returns->body);
		$this->assertEquals($expectedHeaders,$returns->headers);
	}
}
