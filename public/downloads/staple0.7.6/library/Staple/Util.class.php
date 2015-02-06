<?php

/** 
 * A set of static utility functions.
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
class Staple_Util
{
	const STATES_LONG = 1;
	const STATES_SHORT = 2;
	const STATES_BOTH = 3;
	
	public static function StatesArray($type = self::STATES_BOTH)
	{
		$short = array('AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN',
				'MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV',
				'WI','WY');
		
		$long = array('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Florida',
				'Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts',
				'Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico',
				'New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina',
				'South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
		
		switch($type)
		{
			case self::STATES_LONG:
				return $long;
				break;
			case self::STATES_SHORT:
				sort($short);
				return $short;
				break;
			default:
				return array_combine($short, $long);
		}
	}
	
	/**
	 * Grabs and returns only the first word of the sentence. Sometimes that's all you need.
	 * @param string $sentence
	 */
	public static function FirstWord($sentence)
	{
		$words = explode(' ', $sentence);
		return $words[0];
	}
	
	/**
	 * A simple function that is useful for limiting a string to a specified number of words.
	 * @param string $sentence
	 * @param int $limit
	 */
	public static function WordLimit($sentence, $limit)
	{
		$words = explode(' ', trim($sentence));
		$phrase = '';
		if(count($words) < $limit)
		{
			$limit = count($words);
		}
		for($i=0; $i<$limit; $i++)
		{
			$phrase .= $words[$i].' ';
		}
		return trim($phrase);
	}
	
	/**
	 * Multi-dimensional array search function.
	 * @param mixed $needle
	 * @param array $haystack
	 */
	public static function ArraySearch($needle, array $haystack)
	{
		foreach($haystack as $key=>$value)
		{
			if(is_array($value) && !is_array($needle))
			{
				if(($res = self::ArraySearch($needle, $value)) !== false)
				{
					return array_merge(array($key), (array)$res);
				}
			}
			else
			{
				if ($value == $needle)
				{
					return array($key);
				}
			}
		}
		return false;
	}
}

?>