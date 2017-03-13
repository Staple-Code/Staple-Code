<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 4/28/2016
 * Time: 1:47 PM
 */

namespace Stripe\Email;

use SendGrid;
use Staple\Config;
use Exception, stdClass;

class SendGridEmailAdapter extends SendGrid\Email implements EmailAdapter
{
    /**
     * The main SendGrid Object
     * @var SendGrid
     */
    private $sendGrid;

    /**
     * Holds the SendGrid response Object
     * @var stdClass;
     */
    private $response;

    /**
     * The last exception that occurred during execution.
     * @var SendGrid\Exception | Exception;
     */
    private $exception;

    public function __construct()
    {
        $this->createSendGridObject();
        parent::__construct();
    }

    /**
     * Create the SendGrid object to facilitate sending email.
     * @throws \Staple\Exception\ConfigurationException
     */
    private function createSendGridObject()
    {
        $this->sendGrid = new SendGrid(Config::getValue('email','sendgrid_api'));
    }

    public function setBody($body)
    {
        return $this->setHtml($body);
    }

    /**
     * Try to send the email and catch any exceptions that occur.
     * @throws SendGrid\Exception
     */
    public function send()
    {
        try {
            $this->sendGrid->send($this);
            return true;
        } catch (SendGrid\Exception $e) {
            $this->exception = $e;
            return false;
        }
    }

    /**
     * @return stdClass
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param stdClass $response
     * @return $this
     */
    public function setResponse(stdClass $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = NULL;
        $this->addTo($to);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->html;
    }

    /**
     * @return array
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @return array
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param $email
     * @param null $name
     * @return $this
     */
    public function setFrom($email,$name=NULL)
    {
        if(isset($name))
            $this->setFromName($name);
        parent::setFrom($email);
        return $this;
    }

	public function addTo($to)
	{
		// TODO: Implement addTo() method.
	}

	public function addCc($cc)
	{
		// TODO: Implement addCc() method.
	}

	public function addBcc($bcc)
	{
		// TODO: Implement addBcc() method.
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

	public function addAttachment($attachment, $filename = null)
	{
		// TODO: Implement addAttachment() method.
	}

	public function setAttachments(array $attachments)
	{
		// TODO: Implement setAttachments() method.
	}

	public function getAttachments()
	{
		// TODO: Implement getAttachments() method.
	}
}