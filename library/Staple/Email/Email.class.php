<?php

/** 
 * Creates a new Mail object. 
 * 
 * Template File:
 * In the template file a custom comment <!--STAPLE-EMAIL-BODY--> this is where the body of
 * the email is replaced.
 * 
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

use \Exception;
use Staple\Config;
use Staple\Exception\EmailException;
use Staple\View;
use Stripe\Email\EmailAdapter;

class Email
{
	const ADAPTER_DEFAULT = 'Staple\Email\PhpMailAdapter';
	const ADAPTER_SENDGRID = 'Staple\Email\SendGridEmailAdapter';

	const METHOD_MANY = 1;
	const METHOD_SINGLE = 2;

	const EMAIL_FORMAT = '/\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,7})+/';
	/**
	 * Stores callback functions to be processed after sending email.
	 * @var array
	 */
	protected $callbacks = array();
	/**
	 * @var string
	 */
	protected $lastEmailStatus;
	/**
	 * The timestamp the email was sent
	 * @var int
	 */
	protected $sentTime;
	/**
	 * Boolean to choose whether to split the to field into many emails.
	 * @var boolean
	 */
	protected $manyEmails = false;
	/**
	 * The email adapter to send email.
	 * @var EmailAdapter
	 */
	protected $emailAdapter;
	
	/**
	 * Default constructor. Accepts optional values for To, From, CC, and BCC and adapter.
	 * @param string | array $to
	 * @param string $from
	 * @param array $cc
	 * @param array $bcc
	 * @param string $adapter
	 */
	public function __construct($to = NULL, $from = NULL, array $cc = array(), array $bcc = array(), $adapter = self::ADAPTER_DEFAULT)
	{
		//Load the ini settings
		$settings = Config::get('email');
		if($settings['from'] != '')
		{
			$this->setFrom($settings['from']);
		}
		if($settings['bcc'] != '')
		{
			$this->addBcc($settings['bcc']);
		}

		//Setup email adapter
		if($settings['adapter'] != '')
		{
			$this->createEmailAdapter($settings['adapter']);
		}
		elseif(isset($adapter))
		{
			$this->createEmailAdapter($adapter);
		}
		else
		{
			$this->createEmailAdapter(self::ADAPTER_DEFAULT);
		}
		
		//Load any Tos
		if(isset($to))
		{
			if(is_array($to))
			{
				foreach($to as $email)
				{
					$this->addTo($email);
				}
			}
			else
			{
				$this->addTo($to);
			}
		}
		//Load the From
		if(isset($from))
		{
			$this->setFrom($from);
		}
		//Load any CCs
		if(isset($cc))
		{
			if(is_array($cc))
			{
				$this->setCc($cc);
			}
			else
			{
				$this->setCc(array($cc));
			}
		}
		//Load any BCCs
		if(isset($bcc))
		{
			if(is_array($bcc))
			{
				$this->setBcc($bcc);
			}
			else
			{
				$this->setBcc(array($bcc));
			}
		}
	}

	/**
	 * Pass through the method call to the email adapter and look for methods.
	 * @param string $name
	 * @param array $arguments
	 * @throws Exception
	 * @return mixed
	 */
	public function __call($name, array $arguments)
	{
		if(method_exists($this->emailAdapter,$name))
		{
			return call_user_func_array([$this->emailAdapter,$name],$arguments);
		}
		else
		{
			throw new Exception('Method does not exist on object.');
		}
	}

	/**
	 * Factory method to create an Mail object on the fly.
	 * @param string | array $to
	 * @param string $from
	 * @param array $cc
	 * @param array $bcc
	 * @param string $adapter
	 * @return $this
	 */
	public static function create($to = NULL, $from = NULL, array $cc = array(), array $bcc = array(), $adapter = self::ADAPTER_DEFAULT)
	{
		return new self($to, $from, $cc, $bcc, $adapter);
	}
	
	/**
	 * Sends an email. Optional parameters for adding To, Subject, Body, From, CC, and BCC.
	 * @param string | array $to
	 * @param string $subject
	 * @param string $body
	 * @param string $from
	 * @param array $cc
	 * @param array $bcc
	 * @return boolean
	 */
	public function email($to = NULL, $subject = NULL, $body = NULL, $from = NULL, array $cc = array(), array $bcc = array())
	{
		//Check for new To addresses;
		if(isset($to))
		{
			if(is_array($to))
			{
				$this->setTo($to);
			}
			else
			{
				$this->setTo(array($to));
			}
		}
		//Check for a new Subject
		if(isset($subject))
		{
			$this->setSubject($subject);
		}
		//Check for a new Body
		if(isset($body))
		{
			$this->setBody($body);
		}
		
		//Check for a new From
		if(isset($from))
		{
			$this->setFrom($from);
		}
		//Check for new CC addresses
		if(isset($cc))
		{
			if(is_array($cc))
			{
				if(count($cc) > 0)
				{
					$this->setCc($cc);
				}
			}
			else
			{
				$this->setCc(array($cc));
			}
		}
		//Check for new BCC addresses
		if(isset($bcc))
		{
			if(is_array($bcc))
			{
				if(count($bcc) > 0)
				{
					$this->setBcc($bcc);
				}
			}
			else
			{
				$this->setBcc(array($bcc));
			}
		}

		return $this->send();
	}

	/**
	 * Send the email using the email adapter.
	 * @return bool
	 */
	public function send()
	{
		//Split the object into multiples and send to each person separately.
		if($this->getManyEmails() === true)
		{
			$emails = $this->emailAdapter->getTo();
			if(count($emails) > 0)
			{
				foreach($emails as $to)
				{
					$mailObj = clone $this;
					$mailObj->setManyEmails(false);
					$mailObj->emailAdapter = clone $this->emailAdapter;
					$mailObj->emailAdapter->setTo(array($to));

					$success = $mailObj->send();

					$this->setSentTime(time());

					if($success !== true)
					{
						return false;
					}
				}

				$this->setLastEmailStatus(true);

				//Process any callback functions that might exist.
				$this->processCallbacks();

				return true;
			}
		}
		else
		{
			if($this->isReady())
			{
				$success = $this->emailAdapter->send();

				$this->setLastEmailStatus($success);

				$this->setSentTime(time());

				//Process any callback functions that might exist.
				$this->processCallbacks();

				return $success;
			}
		}
		return false;
	}

	/**
	 * Add an email to the To list
	 * @param $to
	 * @return $this
	 * @throws Exception
	 */
	public function addTo($to)
	{
		if(is_numeric($to))
		{
			throw new EmailException('To must be a string.');
		}
		$this->emailAdapter->addTo($to);
		return $this;
	}

	/**
	 * Set the To list to the supplied array
	 * @param array $to
	 * @return $this
	 */
	public function setTo(array $to)
	{
		$this->emailAdapter->setTo($to);
		return $this;
	}

	/**
	 * Add and email to the CC list
	 * @param $to
	 * @return $this
	 * @throws Exception
	 */
	public function addCc($to)
	{
		if(is_numeric($to))
		{
			throw new EmailException('CC must be a string.');
		}
		$this->emailAdapter->addCc($to);
		return $this;
	}

	/**
	 * Set the CC list to the supplied array
	 * @param array $to
	 * @return $this
	 */
	public function setCc(array $to)
	{
		$this->emailAdapter->addCc($to);
		return $this;
	}

	/**
	 * Add an email to the BCC list
	 * @param $to
	 * @return $this
	 * @throws Exception
	 */
	public function addBcc($to)
	{
		if(is_numeric($to))
		{
			throw new EmailException('BCC must be a string.');
		}
		$this->emailAdapter->addBcc($to);
		return $this;
	}

	/**
	 * Set the BBC list as supplied array
	 * @param array $to
	 * @return $this
	 */
	public function setBcc(array $to)
	{
		$this->emailAdapter->addBcc($to);
		return $this;
	}

	/**
	 * Set the from line.
	 * @param string $from
	 * @param string $name
	 * @return $this
	 * @throws Exception
	 */
	public function setFrom($from,$name = NULL)
	{
		if(is_numeric($from))
		{
			throw new EmailException('FROM must be a string.');
		}
		$this->emailAdapter->setFrom($from,$name);
		return $this;
	}

	/**
	 * Set the subject line
	 * @param string $subject
	 * @return $this
	 */
	public function setSubject($subject)
	{
		$this->emailAdapter->setSubject($subject);
		return $this;
	}

	/**
	 * Set the email body
	 * @param string|View $body
	 * @return $this
	 */
	public function setBody($body)
	{
		$this->emailAdapter->setBody($body);
		return $this;
	}
	
	/**
	 * Get the to list
	 * @return string[] $to
	 */
	public function getTo()
	{
		return $this->emailAdapter->getTo();
	}

	/**
	 * Get the CC list
	 * @return array $cc
	 */
	public function getCc()
	{
		return $this->emailAdapter->getCc();
	}

	/**
	 * Get the from string
	 * @return string $from
	 */
	public function getFrom()
	{
		return $this->emailAdapter->getFrom();
	}

	/**
	 * Gets the email subject line
	 * @return string $subject
	 */
	public function getSubject()
	{
		return $this->emailAdapter->getSubject();
	}

	/**
	 * Gets the email body contents
	 * @return mixed|string
	 * @throws Exception
	 */
	public function getBody()
	{
		return $this->emailAdapter->getBody();
	}

	/**
	 * @return array $bcc
	 */
	public function getBcc()
	{
		return $this->emailAdapter->getBcc();
	}

	/**
	 * @return string $lastEmailStatus
	 */
	public function getLastEmailStatus()
	{
		return $this->lastEmailStatus;
	}

	/**
	 * @param string $lastEmailStatus
	 * @return $this
	 */
	public function setLastEmailStatus($lastEmailStatus)
	{
		$this->lastEmailStatus = $lastEmailStatus;
		return $this;
	}

	/**
	 * Process the callback stack
	 * @return $this
	 */
	protected function processCallbacks()
	{
		foreach($this->callbacks as $func)
		{
			call_user_func($func,$this);
		}
		return $this;
	}

	/**
	 * Add a callback to be executed after sending.
	 * @param $callback
	 */
	public function addSendCallback($callback)
	{
		$this->callbacks[] = $callback;
	}

	/**
	 * @return boolean $manyEmails
	 */
	public function getManyEmails()
	{
		return $this->manyEmails;
	}

	/**
	 * @param boolean $manyEmails
	 * @return $this
	 */
	public function setManyEmails($manyEmails)
	{
		$this->manyEmails = (bool)$manyEmails;
		return $this;
	}

	/**
	 * Get the Sent Time of the last email sent.
	 * @return int
	 */
	public function getSentTime()
	{
		return $this->sentTime;
	}

	/**
	 * Set the Sent Time of the email.
	 * @param int $sentTime
	 * @return $this
	 */
	protected function setSentTime($sentTime)
	{
		$this->sentTime = $sentTime;

		return $this;
	}

	/**
	 * @return EmailAdapter
	 */
	public function getEmailAdapter()
	{
		return $this->emailAdapter;
	}

	/**
	 * @param EmailAdapter $emailAdapter
	 * @return $this
	 */
	public function setEmailAdapter(EmailAdapter $emailAdapter)
	{
		$this->emailAdapter = $emailAdapter;
		return $this;
	}

	/**
	 * Creates an instance of the email adapter object.
	 * @param $adapter
	 * @return EmailAdapter
	 * @throws EmailException
	 */
	private function createEmailAdapter($adapter)
	{
		if(class_exists($adapter)) {
			$object = new $adapter();
			if($object instanceof EmailAdapter) {
				$this->setEmailAdapter($object);
				return $object;
			} else {
				throw new EmailException('Email adapter is not of the correct type. Must be an implementation of Staple\\Email\\EmailAdapter.');
			}
		} else {
			throw new EmailException('Failed to initialize email adapter object.');
		}
	}
	
	/**
	 * Checks for a valid email address format.
	 * @param string $email
	 * @return boolean
	 */
	public static function checkEmailFormat($email)
	{
		if(preg_match(self::EMAIL_FORMAT,$email) == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Returns a boolean true if the object has the minimal amount of information to send an email, false otherwise.
	 *
	 * @return boolean
	 */
	public function isReady()
	{
		if(count($this->getTo()) == 0)
		{
			return false;
		}

		if(self::checkEmailFormat($this->getFrom()) === false)
		{
			return false;
		}

		if(strlen($this->getSubject()) == 0)
		{
			return false;
		}

		if(strlen($this->getBody()) == 0)
		{
			return false;
		}

		return true;
	}
}