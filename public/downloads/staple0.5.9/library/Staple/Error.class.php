<?php
/**
 * A class for handling application errors.
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
class Staple_Error
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
	
	private static $lastException;
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
		self::$lastException = $ex;
		ob_clean();
		$main = Staple_Main::get();
		$main->processHeader(true);
		echo "<p>".$ex->getMessage()." Code: ".$ex->getCode()."</p>";
		if(Staple_Config::getValue('errors', 'devmode') == 1)
		{
			echo "<pre>".$ex->getTraceAsString()."</pre>";
			foreach ($ex->getTrace() as $traceln)
			{
				echo "<pre>";
				var_dump($traceln);
				echo "</pre>";
			}
		}
	}
}