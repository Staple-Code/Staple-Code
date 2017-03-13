<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 1/13/2017
 * Time: 10:40 AM
 */

namespace Staple\Email;

use Stripe\Email\EmailAdapter;
use PHPMailer;

class PhpMailerEmailAdapter extends PHPMailer implements EmailAdapter
{
	public function addTo($to)
	{
		// TODO: Implement addTo() method.
	}

	public function setTo($to)
	{
		// TODO: Implement setTo() method.
	}

	public function getTo()
	{
		// TODO: Implement getTo() method.
	}

	public function getCc()
	{
		// TODO: Implement getCc() method.
	}

	public function getBcc()
	{
		// TODO: Implement getBcc() method.
	}

	public function getFrom()
	{
		// TODO: Implement getFrom() method.
	}

	public function setSubject($subject)
	{
		// TODO: Implement setSubject() method.
	}

	public function getSubject()
	{
		// TODO: Implement getSubject() method.
	}

	public function setBody($body)
	{
		// TODO: Implement setBody() method.
	}

	public function getBody()
	{
		// TODO: Implement getBody() method.
	}

	public function setAttachments(array $attachments)
	{
		// TODO: Implement setAttachments() method.
	}
}