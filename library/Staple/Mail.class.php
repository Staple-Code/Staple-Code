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
namespace Staple;

use \Exception;

class Mail
{
	const EMAIL_BODY_FIELD = '<!--STAPLE-EMAIL-BODY-->';
	/**
	 * Whether or not to send email as HTML. Defaults to true.
	 * @var boolean
	 */
	protected $html = true;
	/**
	 * Array of To addresses
	 * @var array[string]
	 */
	protected $to = array();
	/**
	 * Array of Carbon Copied addresses.
	 * @var array[string]
	 */
	protected $cc = array();
	/**
	 * Array of Blind Carbon Copied addresses.
	 * @var array[string]
	 */
	protected $bcc = array();
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
	protected $replyto;
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
	 * 
	 * String listing the location of the email template file.
	 * @var string
	 */
	protected $template;
	/**
	 * Stores callback functions to be processed after sending email.
	 * @var array
	 */
	protected $callbacks = array();
	
	protected $lastEmailStatus;
	
	/**
	 * 
	 * Default constructor. Accepts optional values for To, From, CC, and BCC.
	 * @param string | array $to
	 * @param string $from
	 * @param array $cc
	 * @param array $bcc
	 */
	public function __construct($to = NULL, $from = NULL, array $cc = array(), array $bcc = array())
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
		if($settings['html'] == '0')
		{
			$this->sendAsHtml(false);
		}
		if($settings['server'] != '')
		{
			$this->setServer($settings['server']);
		}
		if(array_key_exists('template', $settings))
			if($settings['template'] != '')
				$this->setTemplate($settings['template']);
		
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
	 * Factory method to create an Mail object on the fly.
	 * @param string | array $to
	 * @param string $from
	 * @param array $cc
	 * @param array $bcc
	 * @return Staple_Mail
	 */
	public static function Create($to = NULL, $from = NULL, array $cc = array(), array $bcc = array())
	{
		return new self($to, $from, $cc);
	}
	
	/**
	 * Sends an email. Optional parameters for  To, Subject, Body, From, CC, and BCC.
	 * @param string | array $to
	 * @param string $subject
	 * @param string $body
	 * @param string $from
	 * @param array $cc
	 * @param array $bcc
	 * @return boolean
	 */
	public function Email($to = NULL, $subject = NULL, $body = NULL, $from = NULL, array $cc = array(), array $bcc = array())
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
		
		//Check that all required fields have been completed before attempting to send.
		if($this->checkMailRequiredFields())
		{
			$toList = implode(', ',$this->to);
			
			//Start the Headers and specify who the email is to.
			$headers = "To: $toList\r\n";
			
			// Enable HTML emailing.
			if($this->html === true)
			{
				// To send HTML mail, the Content-type header must be set
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			}
	
			// Set the from and reply to headers for the email.
			$headers .= "From: {$this->from}\r\n";
			if(isset($this->replyto))
			{
				$headers .= "Reply-To: {$this->replyto}\r\n";
			}
			
			// Carbon Copy an email to specified email.
			if(count($this->cc) > 0)
			{
				$ccList = implode(', ',$this->cc);
				$headers .= "CC: $ccList\r\n";
			}
			
			// Blind Carbon Copy an email to specified email.
			if(count($this->bcc) > 0)
			{
				$bccList = implode(', ',$this->bcc);
				$headers .= "Bcc: $bccList\r\n";
			}
			
			$headers .= 'X-Mailer: PHP/' . phpversion();
			
			//Body Windows Fix
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
				$fixedbody = str_replace("\n.", "\n..", $this->getBody());
			else
				$fixedbody = $this->getBody();
			
			//Base64 encode the email subject
			$subject64 = '=?UTF-8?B?'.base64_encode($this->getSubject()).'?=';
			
			//Send Mail
			$success = mail($toList,$subject64,$fixedbody,$headers);
			$this->setLastEmailStatus($success);
						
			//Process any callback functions that might exist.
			$this->processCallbacks();
			
			return $success;
		}
		return false;
	}
	
	/**
	 * Sends a text message.
	 * @param array | string $to
	 * @param string $subject
	 * @param string $body
	 * @param string $from
	 * @return boolean
	 */
	public function Text($to = NULL, $subject = NULL, $body = NULL, $from = NULL)
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
		
		//Check that all required fields have been completed before attempting to send.
		if($this->checkMailRequiredFields())
		{
			$toList = implode(', ',$this->to);
			
			//Start the Headers and specify who the email is to.
			$headers = "To: $toList\r\n";
	
			// Set the from header for the text.
			$headers .= "From: {$this->from}\r\n";
			
			$headers .= 'X-Mailer: PHP/' . phpversion();
			
			//Send Mail
			//@todo make the program split these into 160 character emails.
			$success = mail($toList,$this->getSubject(),$this->getBody(),$headers);
			$this->setLastEmailStatus($success);
						
			//Process any callback functions that might exist.
			$this->processCallbacks();
			
			return $success;
		}
		return false;
	}
	
	/**
	 * An alias of the Email() function. This function calls Email() sending no parameters.
	 * @see Staple_Mail::Email()
	 */
	public function Send()
	{
		return $this->Email();
	}
	
	/**
	 * Add a single email address to the To list.
	 * @param string $to
	 * @return Staple_Mail
	 */
	public function addTo($to)
	{
		if($this->checkEmailFormat($to))
		{
			if(!in_array($to, $this->to))
			{
				array_push($this->to, $to);
			}
		}
		return $this;
	}
	
	/**
	 * Add a single email address to the CC list.
	 * @param string $to
	 * @return Staple_Mail
	 */
	public function addCc($to)
	{
		if($this->checkEmailFormat($to))
		{
			array_push($this->cc, $to);
		}
		return $this;
	}
	
	/**
	 * Add a single email address to the BCC list.
	 * @param string $to
	 * @return Staple_Mail
	 */
	public function addBcc($to)
	{
		if($this->checkEmailFormat($to))
		{
			array_push($this->bcc, $to);
		}
		return $this;
	}
	
	/**
	 * @return array[string] $to
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * @param array $to
	 * @return Staple_Mail
	 */
	public function setTo(array $to)
	{
		$this->to = array();
		foreach($to as $email)
		{
			if($this->checkEmailFormat($email))
			{
				array_push($this->to, $email);
			}
		}
		return $this;
	}

	/**
	 * @return the $cc
	 */
	public function getCc()
	{
		return $this->cc;
	}

	/**
	 * @param array $cc
	 * @return Staple_Mail
	 */
	public function setCc(array $cc)
	{
		$this->cc = array();
		foreach($cc as $email)
		{
			if($this->checkEmailFormat($email))
			{
				array_push($this->cc, $email);
			}
		}
		return $this;
	}

	/**
	 * @return the $from
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @param string $from
	 * @return Staple_Mail
	 */
	public function setFrom($from)
	{
		if($this->checkEmailFormat($from))
		{
			$this->from = $from;
		}
		return $this;
	}

	/**
	 * @return the $replyto
	 */
	public function getReplyto()
	{
		return $this->replyto;
	}

	/**
	 * @param string $replyto
	 * @return Staple_Mail
	 */
	public function setReplyto($replyto)
	{
		if($this->checkEmailFormat($replyto))
		{
			$this->replyto = $replyto;
		}
		return $this;
	}

	/**
	 * @return the $subject
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @param string $subject
	 * @return Staple_Mail
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
		return $this;
	}

	/**
	 * @return the $body
	 */
	public function getBody()
	{
		if(isset($this->template))
		{
			//Check that the template file exists.
			if(file_exists($this->template))
			{
				$templateFile = file_get_contents($this->template);
				$bodyStr = self::EMAIL_BODY_FIELD;
				if(strpos($templateFile,$bodyStr) !== false)
				{
					return str_replace($bodyStr, $this->body, $templateFile);
				}
				else 
				{
					throw new Exception('Invalid Template File:'.$templateFile, Error::EMAIL_ERROR);
				}
			}
			else
			{
				//Disregard a missing template
				return $this->body;
			}
		}
		else 
		{
			return $this->body;
		}
	}

	/**
	 * @param string $body
	 * @return Staple_Mail
	 */
	public function setBody($body)
	{
		$this->body = $body;
		return $this;
	}
	/**
	 * @return the $bcc
	 */
	public function getBcc()
	{
		return $this->bcc;
	}

	/**
	 * @param array $bcc
	 * @return Staple_Mail
	 */
	public function setBcc(array $bcc)
	{
		foreach($bcc as $email)
		{
			if($this->checkEmailFormat($email))
			{
				array_push($this->bcc, $email);
			}
		}
		return $this;
	}
	
	/**
	 * Sets the SMTP server to connect to.
	 * @param unknown_type $smtp
	 * @return Staple_Mail
	 */
	public function setServer($smtp)
	{
		ini_set('SMTP', $smtp);
		return $this;
	}

	/**
	 * @return the $lastEmailStatus
	 */
	public function getLastEmailStatus()
	{
		return $this->lastEmailStatus;
	}

	/**
	 * @param field_type $lastEmailStatus
	 */
	public function setLastEmailStatus($lastEmailStatus)
	{
		$this->lastEmailStatus = $lastEmailStatus;
		return $this;
	}

	/**
	 * @return the $template
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		if(file_exists($template))
		{
			$this->template = $template;
			return $this;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Find out if HTML is enabled for this email.
	 * @return the $html
	 */
	public function isHtmlEnabled()
	{
		return $this->html;
	}

	/**
	 * Turn HTML on or off for current message.
	 * @param boolean $html
	 * @return Staple_Mail
	 */
	public function sendAsHtml($html)
	{
		$this->html = (bool)$html;
		return $this;
	}
	
	
	protected function processCallbacks()
	{
		foreach($this->callbacks as $func)
		{
			call_user_func($func,$this);
		}
	}
	
	public function addSendCallback($callback)
	{
		$this->callbacks[] = $callback;
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
	
	/**
	 * Checks for a valid email address format.
	 * @param string $email
	 * @return boolean
	 */
	public static function checkEmailFormat($email)
	{
		if(preg_match('/\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,7})+/',$email) == 1)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 * Checks the configuration file that all the keys are available.
	 * @param array $config
	 * @return boolean
	 */
	protected function checkConfig($config)
	{
		$keys = array('html','from','bcc','server');
		foreach($keys as $value)
		{
			if(!array_key_exists($value, $config))
			{
				return false;
			}
		}
		return true;
	}
}

?>