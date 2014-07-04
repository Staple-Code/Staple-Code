<?php
/** 
 * Formats the Phone Number to a correct format.
 * @todo fix the formatting errors in this function
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
class Staple_Form_Filter_PhoneFormat extends Staple_Form_Filter
{
	const REGEX = '/^(\d{0,4})?[\.\-\/ ]?\(?(\d{3})\)?[\.\-\/ ]?(\d{3})[\.\-\/ ]?(\d{4})$/';
	/**
	 * 
	 * @see Staple_Form_Filter::filter()
	 */
	public function filter($text)
	{
		if(preg_match(self::REGEX, $text, $matches))
		{
			return trim("$matches[1] ({$matches[2]}) {$matches[3]}-{$matches[4]}");
		}
		else
		{
			return '';
		}
	}
	/**
	 * 
	 * @see Staple_Form_Filter::getName()
	 */
	public function getName()
	{
		return 'phonefilter';
	}

}

?>