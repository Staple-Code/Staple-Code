<?php
/** 
 * @todo the check() function is incomplete 
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
class Staple_Form_Validate_UploadedFile extends Staple_Form_Validator
{
	const DEFAULT_ERROR = 'File is not valid';
	
	public function __construct($usermsg = NULL)
	{
		if(isset($usermsg))
		{
			parent::__construct($usermsg);
		}
	}

	/**
	 * 
	 * @param  mixed $data
 
	 * @return  bool
	  
	 * @see Staple_Form_Validator::check()
	 */
	public function check($data)
	{
		if($this->strict === true)
		{
			if($this->equal === $data)
			{
				return true;
			}
			else
			{
				$this->addError();
			}
		}
		else
		{
			if($this->equal == $data)
			{
				return true;
			}
			else
			{
				$this->addError();
			}
		}
		return false;
	}
}

?>