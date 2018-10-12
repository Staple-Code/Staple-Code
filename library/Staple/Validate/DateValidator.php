<?php
/** 
 * Validate a Date field.
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

class DateValidator extends BaseValidator
{
	// American date string
	const REGEX_AMERICAN = '/^(((0?[13578]|1[02])[\- \/\.](0?[1-9]|[12][0-9]|3[01]))|(0?2[\- \/\.](0?[1-9]|[12][0-9]))|((0?[469]|11)[\- \/\.](0?[1-9]|[12][0-9]|3[0])))[\- \/\.]((19|20)\d\d)$/';
	// ISO date string
	const REGEX_ISO = '/^((19|20)\d\d)[\- \/\.](((0?[13578]|1[02])[\- \/\.](0?[1-9]|[12][0-9]|3[01]))|(0?2[\- \/\.](0?[1-9]|[12][0-9]))|((0?[469]|11)[\- \/\.](0?[1-9]|[12][0-9]|3[0])))$/';

	const DEFAULT_ERROR = 'Field must be a valid date.';

	/**
	 * Match list
	 * @var array
	 */
	private $matches;

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
	 */
	public function check($data): bool
	{
		if(preg_match(self::REGEX_AMERICAN, $data, $this->matches))
		{
			return true;
		}
		else
		{
			if(preg_match(self::REGEX_ISO, $data, $this->matches))
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
}