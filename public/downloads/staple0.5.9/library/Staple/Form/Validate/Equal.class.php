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
 */
class Staple_Form_Validate_Equal extends Staple_Form_Validator
{
	protected $strict;
	protected $equal;
	
	public function __construct($equal, $strict = false)
	{
		$this->equal = $equal;
		$this->strict = (bool)$strict;
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
				$this->addError('Data is not equal.');
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
				$this->addError('Data is not equal.');
			}
		}
		return false;
	}
}

?>