<?php
/** 
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
namespace Staple\Validate;

class RegexValidator extends BaseValidator
{
	/**
	 * Constant for validating usernames. Usernames must be 6-50 characters and include only letters, numbers and underscores.
	 * @var string
	 */
	const USERNAME = '/^[a-zA-Z0-9_]{6,50}$/';
	/**
	 * Constant for complex passwords. Requires minimum length a 8 characters, one lowercase letter, one uppercase letter, one number, a special character and no whitespace.
	 * @var string
	 */
	const PASSWORD = '/(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_])(?=^.*[^\s].*$).*$/';
	
	/**
	 * A string regular expression
	 * @var string
	 */
	protected $regex;
	
	/**
	 * Array of Matches
	 * @var array
	 */
	protected $matches;

	/**
	 * Constructor sets the regex value to validate against and supports an optional user message.
	 * @param string $regex
	 * @param string $userMessage
	 */
	function __construct($regex, $userMessage = NULL)
	{
		$this->setRegex($regex);
		parent::__construct($userMessage);
	}
	
	/**
	 * Set the regex to validate against.
	 * @param string $regex
	 * @return $this
	 */
	public function setRegex($regex): RegexValidator
	{
		$this->regex = $regex;
		return $this;
	}
	
	/**
	 * Returns the regex value
	 * @return string $regex
	 */
	public function getRegex(): string
	{
		return $this->regex;
	}
	
	/**
	 * @return array
	 */
	public function getMatches(): array
	{
		return $this->matches;
	}

	/**
	 * @param  mixed $data
	 * @return  bool
	 * @see Staple_Form_Validator::check()
	 */
	public function check($data): bool
	{
		if(preg_match($this->regex, $data, $this->matches) >= 1)
		{
			return true;
		}
		else
		{
			$this->addError();
			return false;
		}
	}
}