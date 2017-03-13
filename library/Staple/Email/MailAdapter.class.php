<?php
/**
 * PHP Mail Email Adapter class. This class implements emails based on the PHP mail() function.
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

namespace Staple\Email;

use Staple\Config;
use Staple\Exception\EmailException;

class MailAdapter implements EmailAdapter
{
	/**
	 * Whether or not to send email as HTML. Defaults to true.
	 * @var boolean
	 */
	protected $html = true;
	/**
	 * Array of To addresses
	 * @var string[]
	 */
	protected $to = [];
	/**
	 * Array of Carbon Copied addresses.
	 * @var string[]
	 */
	protected $cc = [];
	/**
	 * Array of Blind Carbon Copied addresses.
	 * @var string[]
	 */
	protected $bcc = [];
	/**
	 * The From field.
	 * @var string
	 */
	protected $from;
	/**
	 * The reply-to field. (Optional) Only required if you want the email/text to reply to a
	 * different address than specified in the from field.
	 * @var string
	 */
	protected $replyTo;
	/**
	 * Subject of the email/text
	 * @var string
	 */
	protected $subject;
	/**
	 *
	 * The Body of the email/text.
	 * @var string
	 */
	protected $body;
	/**
	 * @var string[]
	 */
	protected $attachments = [];

	public function __construct()
	{
		try
		{
			if(Config::exists('email', 'from'))
			{
				$this->setFrom(Config::getValue('email', 'from'));
			}
			if(Config::exists('email', 'bcc'))
			{
				$this->addBcc(Config::getValue('email', 'bcc'));
			}
			if(Config::exists('email', 'html'))
			{
				$this->setHtml(Config::getValue('email', 'html'));
			}
		}
		catch(EmailException $e) {}	//Ignore invalid emails on object creation.
	}

	public function send()
	{
		// TODO: Implement send() method.

		//Check that all required fields have been completed before attempting to send.
		if($this->checkMailRequiredFields())
		{
			//Return Characters.
			$rn = "\r\n";

			//Get the to list.
			$toList = implode(', ', $this->getTo());

			//Body Windows Fix
			if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
				$message = str_replace("\n.", "\n..", $this->getBody());
			else
				$message = $this->getBody();

			//Start the Headers and specify who the email is to.
			$headers = 'To: ' . $toList . $rn;

			// Set the from and reply to headers for the email.
			$headers .= 'From: '.$this->getFrom() . $rn;

			//MIME Version
			$headers .= 'MIME-Version: 1.0' . $rn;

			//Reply To Header
			if(isset($this->replyto))
			{
				$headers .= 'Reply-To: ' . $this->getReplyTo() . $rn;
			}

			// Carbon Copy an email to specified email.
			if(count($this->getCc()) > 0)
			{
				$ccList = implode(', ', $this->getCc());
				$headers .= 'CC: '.$ccList . $rn;
			}

			// Blind Carbon Copy an email to specified email.
			if(count($this->getBcc()) > 0)
			{
				$bccList = implode(', ', $this->getBcc());
				$headers .= 'Bcc: ' . $bccList . $rn;
			}

			//PHP Mailer Header
			$headers .= 'X-Mailer: PHP/' . phpversion() . $rn;

			if($this->hasAttachments())
			{
				//http://stackoverflow.com/questions/12301358/send-attachments-with-php-mail

				// Hash content separator
				$separator = md5(time());

				// Change to multipart header
				$headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $rn;
				$headers .= "Content-Transfer-Encoding: 7bit" . $rn;
				$headers .= "This is a MIME encoded message." . $rn;

				// message
				$body = "--" . $separator . $rn;
				$body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $rn;
				$body .= "Content-Transfer-Encoding: 8bit" . $rn;
				$body .= $message . $rn;

				// Attachments
				foreach($this->getAttachments() as $attachment)
				{
					$filename = basename($attachment);

					$content = file_get_contents($attachment);
					$content = chunk_split(base64_encode($content));

					$body .= "--" . $separator . $rn;
					$body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $rn;
					$body .= "Content-Transfer-Encoding: base64" . $rn;
					$body .= "Content-Disposition: attachment" . $rn;
					$body .= $content . $rn;
					$body .= "--" . $separator . "--";
				}
			}
			else
			{
				// Enable HTML emailing.
				if($this->isHtml() === true)
				{
					// To send HTML mail, the Content-type header must be set
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . $rn;
				}

				$body = $message;
			}

			//Base64 encode the email subject
			$subject = '=?UTF-8?B?' . base64_encode($this->getSubject()) . '?=';

			//Send the Email
			return $this->performSend($toList, $subject, $body, $headers);
		}

		return false;
	}

	/**
	 * Invoke the mail() function in PHP to send the email.
	 * @param $to
	 * @param $subject
	 * @param $body
	 * @param $headers
	 * @return bool
	 */
	protected function performSend($to, $subject, $body, $headers)
	{
		return mail($to, $subject, $body, $headers);
	}

	/**
	 * Add an email to the list of to email addresses.
	 * @param $to
	 * @return $this
	 * @throws EmailException
	 */
	public function addTo($to)
	{
		if(Email::checkEmailFormat($to))
		{
			if(array_search($to, $this->to) === false)
				$this->to[] = $to;
		}
		else
		{
			throw new EmailException('Invalid email format');
		}

		return $this;
	}

	/**
	 * Set the $to array of the object.
	 * @param $to
	 * @return $this
	 * @throws EmailException
	 */
	public function setTo($to)
	{
		if(is_array($to))
		{
			$this->to = [];
			foreach($to as $email)
			{
				$this->addTo($email);
			}
		}
		return $this;
	}

	/**
	 * Returns the array of $to email addresses.
	 * @return \string[]
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * Add and email to the CC list of emails
	 * @param $cc
	 * @return $this
	 * @throws EmailException
	 */
	public function addCc($cc)
	{
		if(Email::checkEmailFormat($cc))
		{
			if(array_search($cc, $this->cc) === false)
				$this->cc[] = $cc;
		}
		else
		{
			throw new EmailException('Invalid email format');
		}

		return $this;
	}

	/**
	 * Set the $cc array of the object.
	 * @param $cc
	 * @return $this
	 * @throws EmailException
	 */
	public function setCc($cc)
	{
		if(is_array($cc))
		{
			$this->cc = [];
			foreach($cc as $email)
			{
				$this->addCc($email);
			}
		}
		return $this;
	}

	/**
	 * Return the CC array
	 * @return \string[]
	 */
	public function getCc()
	{
		return $this->cc;
	}

	/**
	 * Add an email to the BCC list of emails
	 * @param $bcc
	 * @return $this
	 * @throws EmailException
	 */
	public function addBcc($bcc)
	{
		if(Email::checkEmailFormat($bcc))
		{
			if(array_search($bcc, $this->bcc) === false)
				$this->bcc[] = $bcc;
		}
		else
		{
			throw new EmailException('Invalid email format');
		}

		return $this;
	}

	/**
	 * Set the $bcc array of the object.
	 * @param $bcc
	 * @return $this
	 * @throws EmailException
	 */
	public function setBcc($bcc)
	{
		if(is_array($bcc))
		{
			$this->bcc = [];
			foreach($bcc as $email)
			{
				$this->addBcc($email);
			}
		}
		return $this;
	}

	/**
	 * Return the BCC array
	 * @return \string[]
	 */
	public function getBcc()
	{
		return $this->bcc;
	}

	/**
	 * Set the from email. Optional name parameter.
	 * @param $from
	 * @param string $name
	 * @return $this
	 */
	public function setFrom($from, $name = null)
	{
		if(is_null($name))
		{
			$this->from = $from;
		}
		else
		{
			$this->from = $name . ' <' . $from . '>';
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @param $subject
	 * @return $this
	 */
	public function setSubject($subject)
	{
		$this->subject = (string)$subject;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * Set the body. Will attempt to compress objects back down to strings.
	 * @param $body
	 * @return $this
	 */
	public function setBody($body)
	{
		$this->body = (string)$body;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	public function addAttachment($attachment, $filename = null)
	{
		// TODO: Implement addAttachment() method.
	}

	/**
	 * @param array $attachments
	 * @return $this
	 */
	public function setAttachments(array $attachments)
	{
		$this->attachments = $attachments;
		return $this;
	}

	/**
	 * @return \string[]
	 */
	public function getAttachments()
	{
		return $this->attachments;
	}

	/**
	 * Returns a boolean if there are items in the attachments array.
	 * @return bool
	 */
	public function hasAttachments()
	{
		return (count($this->attachments) > 0) ? true : false;
	}

	/**
	 * Find out if HTML is enabled for this email.
	 * @return string $html
	 */
	public function isHtml()
	{
		return $this->html;
	}

	/**
	 * Turn HTML on or off for current message.
	 * @param boolean $html
	 * @return $this
	 */
	public function setHtml($html)
	{
		$this->html = (bool)$html;
		return $this;
	}

	/**
	 * Sets the SMTP server to connect to.
	 * @param string $smtp
	 * @return $this
	 */
	public function setServer($smtp)
	{
		ini_set('SMTP', $smtp);
		return $this;
	}

	/**
	 * @return string $replyTo
	 */
	public function getReplyTo()
	{
		return $this->replyTo;
	}

	/**
	 * @param string $replyTo
	 * @return $this
	 */
	public function setReplyTo($replyTo)
	{
		if(Email::checkEmailFormat($replyTo))
		{
			$this->replyTo = $replyTo;
		}
		return $this;
	}

	/**
	 * Checks that the required fields are completed in the object before attempting to send.
	 * @return boolean
	 */
	protected function checkMailRequiredFields()
	{
		$errors = 0;

		if(count($this->to) < 1)
		{
			$errors++;
		}
		if(!isset($this->from))
		{
			$errors++;
		}
		if(!isset($this->subject))
		{
			$errors++;
		}
		if(!isset($this->body))
		{
			$errors++;
		}
		if($errors < 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}