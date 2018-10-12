<?php
/** 
 * Validate a floating point number.
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
namespace Staple\Validate;

class FloatValidator extends BaseValidator
{
	const DEFAULT_ERROR = 'Field value must be a floating point number.';

	/**
	 * @param mixed $data
	 * @return bool
	 */
	public function check($data): bool
	{
		if(ctype_digit(str_replace(array('.','-'), '', $data)) === true)
		{
			return true;
		}
		else
		{
			$this->addError(self::DEFAULT_ERROR);
		}
		return false;
	}
}