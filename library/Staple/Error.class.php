<?php
/**
 * A class for handling application errors.
 * @todo this class needs to be redesigned
 * 
 * @author Ironpilot
 * @copyright Copywrite (c) 2011, STAPLE CODE
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
namespace Staple;

use \SplObjectStorage, \SplSubject, \SplObserver, \ErrorException, \Exception, \Staple\Exception\PageNotFoundException;

class Error implements SplSubject
{
	const PAGE_NOT_FOUND = 404;
	const APPLICATION_ERROR = 500;
	const LOADER_ERROR = 501;
	const DB_ERROR = 502;
	const AUTH_ERROR = 503;
	const EMAIL_ERROR = 504;
	const FORM_ERROR = 505;
	const VALIDATION_ERROR = 506;
	const LINK_ERROR = 507;
	
	/**
	 * The object observers
	 * @var SplObjectStorage
	 */
	private $_observers;
	
	/**
	 * This is the callback for error handling.
	 * @var SplObserver
	 */
	protected $logger;
	
	/**
	 * The last exception that was thrown by the system.
	 * @var Exception
	 */
	private static $lastException;
	
	/**
	 * The default constructor.
	 */
	public function __construct()
	{
	    $this->_observers = new SplObjectStorage();
	}
	
	/**
	 * Set the logger Object
	 * @return SplObserver $logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}
	
	/**
	 * Get the Logger Object
	 * @param SplObserver $logger        	
	 */
	public function setLogger(SplObserver $logger)
	{
		$this->attach($logger);
		$this->logger = $logger;
		return $this;
	}

	/**
	 * @return Exception $lastException
	 */
	public function getLastException()
	{
		return self::$lastException;
	}

	/**
	 * @param Exception $lastException
	 */
	private function setLastException(Exception $lastException)
	{
		self::$lastException = $lastException;
		return $this;
	}

	/**
	 * 
	 * handleError catches PHP Errors and displays an error page with the error details.
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public static function handleError($errno, $errstr, $errfile, $errline)
	{
		//Convert Errors into exceptions.
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	/**
	 * 
	 * handleException catches Exceptions and displays an error page with the details.
	 * @todo create and implement and error controller that can display custom errors
	 * per application.
	 * @param Exception $ex
	 */
	public function handleException(Exception $ex)
	{
	    //handle the error
		$this->setLastException($ex);
		
		//Notify observers
		$this->notify();
		
		//Clear the output buffer
		ob_clean();
		
		//Get the Front Controller
		$main = Main::get();

		//Set the HTTP response code
		if($ex instanceof PageNotFoundException)
		{
			http_response_code(404);
		}
		else
		{
			http_response_code(500);
		}

		ob_start();

		//Echo the error message
		echo "<p>".$ex->getMessage()." Code: ".$ex->getCode()."</p>";
		
		//Echo details if in dev mode
		if($main->inDevMode())
		{
			if(($p = $ex->getPrevious()) instanceof Exception)
			{
				echo "<p><b>Previous Error:</b> ".$p->getMessage." Code: ".$p->getCode()."</p>";
			}
			echo "<pre>".$ex->getTraceAsString()."</pre>";
			foreach ($ex->getTrace() as $traceln)
			{
				echo "<pre>";
				var_dump($traceln);
				echo "</pre>";
			}
		}

		$buffer = ob_get_contents();
		ob_end_clean();
		
		//If the site uses layout, build the default layout and put the error message inside.
		if(Layout::layoutExists(Config::getValue('layout', 'default')))
		{
			//Create the layout object and build the layout
			$layout = new Layout(Config::getValue('layout', 'default'));
			$layout->build($buffer);
		}
		else
		{
			echo $buffer;
		}
	}
	
	/**
	 * Stub function for the future addition of error controller functionality
	 */
	private function dispatchErrorController()
	{
	    
	}
	
	/* (non-PHPdoc)
	 * @see SplSubject::attach()
	 */
	public function attach(SplObserver $observer)
	{
		$this->_observers->attach($observer);
	}

	/* (non-PHPdoc)
	 * @see SplSubject::detach()
	 */
	public function detach(SplObserver $observer)
	{
		$this->_observers->detach($observer);
	}

	/* (non-PHPdoc)
	 * @see SplSubject::notify()
	 */
	public function notify()
	{
		foreach($this->_observers as $observer)
		{
		    $observer->update($this);
		}
	}

}