<?php

require_once 'public\downloads\staple0.5.9\library\Staple\Mail.class.php';

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * Staple_Mail test case.
 */
class Staple_MailTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var Staple_Mail
	 */
	private $Staple_Mail;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		// TODO Auto-generated Staple_MailTest::setUp()
		

		$this->Staple_Mail = new Staple_Mail(/* parameters */);
	
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		// TODO Auto-generated Staple_MailTest::tearDown()
		

		$this->Staple_Mail = null;
		
		parent::tearDown();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}

	/**
	 * Tests Staple_Mail->__construct()
	 */
	public function test__construct()
	{
		// TODO Auto-generated Staple_MailTest->test__construct()
		$this->markTestIncomplete("__construct test not implemented");
		
		$this->Staple_Mail->__construct(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail::Create()
	 */
	public function testCreate()
	{
		// TODO Auto-generated Staple_MailTest::testCreate()
		$this->markTestIncomplete("Create test not implemented");
		
		Staple_Mail::Create(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->Email()
	 */
	public function testEmail()
	{
		// TODO Auto-generated Staple_MailTest->testEmail()
		$this->markTestIncomplete("Email test not implemented");
		
		$this->Staple_Mail->Email(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->Text()
	 */
	public function testText()
	{
		// TODO Auto-generated Staple_MailTest->testText()
		$this->markTestIncomplete("Text test not implemented");
		
		$this->Staple_Mail->Text(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->Send()
	 */
	public function testSend()
	{
		// TODO Auto-generated Staple_MailTest->testSend()
		$this->markTestIncomplete("Send test not implemented");
		
		$this->Staple_Mail->Send(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->addTo()
	 */
	public function testAddTo()
	{
		// TODO Auto-generated Staple_MailTest->testAddTo()
		$this->markTestIncomplete("addTo test not implemented");
		
		$this->Staple_Mail->addTo(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->addCc()
	 */
	public function testAddCc()
	{
		// TODO Auto-generated Staple_MailTest->testAddCc()
		$this->markTestIncomplete("addCc test not implemented");
		
		$this->Staple_Mail->addCc(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->addBcc()
	 */
	public function testAddBcc()
	{
		// TODO Auto-generated Staple_MailTest->testAddBcc()
		$this->markTestIncomplete("addBcc test not implemented");
		
		$this->Staple_Mail->addBcc(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->getTo()
	 */
	public function testGetTo()
	{
		// TODO Auto-generated Staple_MailTest->testGetTo()
		$this->markTestIncomplete("getTo test not implemented");
		
		$this->Staple_Mail->getTo(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setTo()
	 */
	public function testSetTo()
	{
		// TODO Auto-generated Staple_MailTest->testSetTo()
		$this->markTestIncomplete("setTo test not implemented");
		
		$this->Staple_Mail->setTo(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->getCc()
	 */
	public function testGetCc()
	{
		// TODO Auto-generated Staple_MailTest->testGetCc()
		$this->markTestIncomplete("getCc test not implemented");
		
		$this->Staple_Mail->getCc(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setCc()
	 */
	public function testSetCc()
	{
		// TODO Auto-generated Staple_MailTest->testSetCc()
		$this->markTestIncomplete("setCc test not implemented");
		
		$this->Staple_Mail->setCc(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->getFrom()
	 */
	public function testGetFrom()
	{
		// TODO Auto-generated Staple_MailTest->testGetFrom()
		$this->markTestIncomplete("getFrom test not implemented");
		
		$this->Staple_Mail->getFrom(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setFrom()
	 */
	public function testSetFrom()
	{
		// TODO Auto-generated Staple_MailTest->testSetFrom()
		$this->markTestIncomplete("setFrom test not implemented");
		
		$this->Staple_Mail->setFrom(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->getReplyto()
	 */
	public function testGetReplyto()
	{
		// TODO Auto-generated Staple_MailTest->testGetReplyto()
		$this->markTestIncomplete("getReplyto test not implemented");
		
		$this->Staple_Mail->getReplyto(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setReplyto()
	 */
	public function testSetReplyto()
	{
		// TODO Auto-generated Staple_MailTest->testSetReplyto()
		$this->markTestIncomplete("setReplyto test not implemented");
		
		$this->Staple_Mail->setReplyto(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->getSubject()
	 */
	public function testGetSubject()
	{
		// TODO Auto-generated Staple_MailTest->testGetSubject()
		$this->markTestIncomplete("getSubject test not implemented");
		
		$this->Staple_Mail->getSubject(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setSubject()
	 */
	public function testSetSubject()
	{
		// TODO Auto-generated Staple_MailTest->testSetSubject()
		$this->markTestIncomplete("setSubject test not implemented");
		
		$this->Staple_Mail->setSubject(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->getBody()
	 */
	public function testGetBody()
	{
		// TODO Auto-generated Staple_MailTest->testGetBody()
		$this->markTestIncomplete("getBody test not implemented");
		
		$this->Staple_Mail->getBody(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setBody()
	 */
	public function testSetBody()
	{
		// TODO Auto-generated Staple_MailTest->testSetBody()
		$this->markTestIncomplete("setBody test not implemented");
		
		$this->Staple_Mail->setBody(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->getBcc()
	 */
	public function testGetBcc()
	{
		// TODO Auto-generated Staple_MailTest->testGetBcc()
		$this->markTestIncomplete("getBcc test not implemented");
		
		$this->Staple_Mail->getBcc(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setBcc()
	 */
	public function testSetBcc()
	{
		// TODO Auto-generated Staple_MailTest->testSetBcc()
		$this->markTestIncomplete("setBcc test not implemented");
		
		$this->Staple_Mail->setBcc(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setServer()
	 */
	public function testSetServer()
	{
		// TODO Auto-generated Staple_MailTest->testSetServer()
		$this->markTestIncomplete("setServer test not implemented");
		
		$this->Staple_Mail->setServer(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->getTemplate()
	 */
	public function testGetTemplate()
	{
		// TODO Auto-generated Staple_MailTest->testGetTemplate()
		$this->markTestIncomplete("getTemplate test not implemented");
		
		$this->Staple_Mail->getTemplate(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->setTemplate()
	 */
	public function testSetTemplate()
	{
		// TODO Auto-generated Staple_MailTest->testSetTemplate()
		$this->markTestIncomplete("setTemplate test not implemented");
		
		$this->Staple_Mail->setTemplate(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->isHtmlEnabled()
	 */
	public function testIsHtmlEnabled()
	{
		// TODO Auto-generated Staple_MailTest->testIsHtmlEnabled()
		$this->markTestIncomplete("isHtmlEnabled test not implemented");
		
		$this->Staple_Mail->isHtmlEnabled(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail->sendAsHtml()
	 */
	public function testSendAsHtml()
	{
		// TODO Auto-generated Staple_MailTest->testSendAsHtml()
		$this->markTestIncomplete("sendAsHtml test not implemented");
		
		$this->Staple_Mail->sendAsHtml(/* parameters */);
	
	}

	/**
	 * Tests Staple_Mail::checkEmailFormat()
	 */
	public function testCheckEmailFormat()
	{
		// TODO Auto-generated Staple_MailTest::testCheckEmailFormat()
		$this->markTestIncomplete(
		"checkEmailFormat test not implemented");
		
		Staple_Mail::checkEmailFormat(/* parameters */);
	
	}

}

