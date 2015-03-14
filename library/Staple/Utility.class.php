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
namespace Staple;

class Utility
{
	const STATES_LONG = 1;
	const STATES_SHORT = 2;
	const STATES_BOTH = 3;

	public static $stateAbbreviations = array('AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN',
'MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV',
'WI','WY');

	public static $stateNames = array('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Florida',
'Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts',
'Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico',
'New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina',
'South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming');


	/**
	 * Inflection rules from CakePHP framework
	 *
	 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
	 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
	 *
	 * Licensed under The MIT License
	 * For full copyright and license information, please see the LICENSE.txt
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
	 * @link          http://cakephp.org CakePHP(tm) Project
	 * @since         0.2.9
	 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
	 */

	/**
	 * Pluralization rules
	 * @var array
	 */
	protected static $pluralRules = [
		'/(s)tatus$/i' => '\1tatuses',
		'/(quiz)$/i' => '\1zes',
		'/^(ox)$/i' => '\1\2en',
		'/([m|l])ouse$/i' => '\1ice',
		'/(matr|vert|ind)(ix|ex)$/i' => '\1ices',
		'/(x|ch|ss|sh)$/i' => '\1es',
		'/([^aeiouy]|qu)y$/i' => '\1ies',
		'/(hive)$/i' => '\1s',
		'/(?:([^f])fe|([lre])f)$/i' => '\1\2ves',
		'/sis$/i' => 'ses',
		'/([ti])um$/i' => '\1a',
		'/(p)erson$/i' => '\1eople',
		'/(?<!u)(m)an$/i' => '\1en',
		'/(c)hild$/i' => '\1hildren',
		'/(buffal|tomat)o$/i' => '\1\2oes',
		'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
		'/us$/i' => 'uses',
		'/(alias)$/i' => '\1es',
		'/(ax|cris|test)is$/i' => '\1es',
		'/s$/' => 's',
		'/^$/' => '',
		'/$/' => 's',
	];

	/**
	 * Singularization rules
	 * @var array
	 */
	protected static $singularRules = [
		'/(s)tatuses$/i' => '\1\2tatus',
		'/^(.*)(menu)s$/i' => '\1\2',
		'/(quiz)zes$/i' => '\\1',
		'/(matr)ices$/i' => '\1ix',
		'/(vert|ind)ices$/i' => '\1ex',
		'/^(ox)en/i' => '\1',
		'/(alias)(es)*$/i' => '\1',
		'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
		'/([ftw]ax)es/i' => '\1',
		'/(cris|ax|test)es$/i' => '\1is',
		'/(shoe)s$/i' => '\1',
		'/(o)es$/i' => '\1',
		'/ouses$/' => 'ouse',
		'/([^a])uses$/' => '\1us',
		'/([m|l])ice$/i' => '\1ouse',
		'/(x|ch|ss|sh)es$/i' => '\1',
		'/(m)ovies$/i' => '\1\2ovie',
		'/(s)eries$/i' => '\1\2eries',
		'/([^aeiouy]|qu)ies$/i' => '\1y',
		'/(tive)s$/i' => '\1',
		'/(hive)s$/i' => '\1',
		'/(drive)s$/i' => '\1',
		'/([le])ves$/i' => '\1f',
		'/([^rfoa])ves$/i' => '\1fe',
		'/(^analy)ses$/i' => '\1sis',
		'/(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
		'/([ti])a$/i' => '\1um',
		'/(p)eople$/i' => '\1\2erson',
		'/(m)en$/i' => '\1an',
		'/(c)hildren$/i' => '\1\2hild',
		'/(n)ews$/i' => '\1\2ews',
		'/eaus$/' => 'eau',
		'/^(.*us)$/' => '\\1',
		'/s$/i' => ''
	];

	/**
	 * Words where pluralization should not be changed
	 * @var array
	 */
	protected static $unchangedRules = [
		'/.*[nrlm]ese/', '/.*data/', '/.*deer/', '/.*fish/', '/.*measles/', '/.*ois/',
		'/.*pox/', '/.*sheep/', '/feedback/', '/stadia/', '/.*?media/',
		'/chassis/', '/clippers/', '/debris/', '/diabetes/', '/equipment/', '/gallows/',
		'/graffiti/', '/headquarters/', '/information/', '/innings/', '/news/', '/nexus/',
		'/proceedings/', '/research/', '/sea[- ]bass/', '/series/', '/species/', '/weather/'
	];

	/**
	 * Cache
	 * @var array
	 */
	protected static $wordCache = [
		'irregular' => ['atlas' => 'atlases',
			'beef' => 'beefs',
			'brief' => 'briefs',
			'brother' => 'brothers',
			'cafe' => 'cafes',
			'child' => 'children',
			'cookie' => 'cookies',
			'corpus' => 'corpuses',
			'cow' => 'cows',
			'criterion' => 'criteria',
			'ganglion' => 'ganglions',
			'genie' => 'genies',
			'genus' => 'genera',
			'goose'	=> 'geese',
			'graffito' => 'graffiti',
			'hoof' => 'hoofs',
			'loaf' => 'loaves',
			'man' => 'men',
			'money' => 'monies',
			'mongoose' => 'mongooses',
			'move' => 'moves',
			'mythos' => 'mythoi',
			'niche' => 'niches',
			'numen' => 'numina',
			'occiput' => 'occiputs',
			'octopus' => 'octopuses',
			'opus' => 'opuses',
			'ox' => 'oxen',
			'penis' => 'penises',
			'person' => 'people',
			'sex' => 'sexes',
			'soliloquy' => 'soliloquies',
			'testis' => 'testes',
			'trilby' => 'trilbys',
			'turf' => 'turfs',
			'potato' => 'potatoes',
			'hero' => 'heroes',
			'tooth' => 'teeth',
			'foot' => 'feet',
			'foe' => 'foes'],
		'singular' => [],
		'plural' => []
	];

	/**
	 * Return an array of states
	 * @param int $type
	 * @return array
	 */
	public static function statesArray($type = self::STATES_BOTH)
	{
		switch($type)
		{
			case self::STATES_LONG:
				return self::$stateNames;
				break;
			case self::STATES_SHORT:
				return self::$stateAbbreviations;
				break;
			default:
				return array_combine(self::$stateAbbreviations, self::$stateNames);
		}
	}
	
	/**
	 * Grabs and returns only the first word of the sentence. Sometimes that's all you need.
	 * @param string $sentence
	 * @return string
	 */
	public static function firstWord($sentence)
	{
		return substr($sentence, 0, strpos($sentence, ' '));
	}
	
	/**
	 * A simple function that is useful for limiting a string to a specified number of words.
	 * @param string $sentence
	 * @param int $limit
	 * @return string
	 */
	public static function wordLimit($sentence, $limit)
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
	 * A simple function that is useful for counting the number of words in a string.
	 * @param string $sentence
	 * @return int
	 */
	public static function wordCount($sentence)
	{
		return count(explode(' ', trim($sentence)));
	}
	
	/**
	 * Multi-dimensional recursive array search function.
	 * @param mixed $needle
	 * @param array $haystack
	 * @return array | bool
	 */
	public static function arraySearch($needle, array $haystack)
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

	public static function snakeCase($string)
	{
		//@todo implement this method
	}

	/**
	 * Pluralize a word.
	 * @param $word
	 * @return mixed
	 */
	public static function pluralize($word)
	{
		//Check for cached conversion
		if (isset(static::$wordCache['plural'][$word]))
		{
			return static::$wordCache['plural'][$word];
		}

		//Check for an irregular conversion
		if (isset(static::$wordCache['irregular'][$word]))
		{
			return static::$wordCache['irregular'][$word];
		}

		//Check for unchanged words
		foreach (static::$unchangedRules as $rule)
		{
			if (preg_match($rule, $word))
			{
				static::$wordCache['plural'][$word] = $word;
				return $word;
			}
		}

		//Check pluralization rules
		foreach (static::$pluralRules as $rule => $replacement)
		{
			if (preg_match($rule, $word))
			{
				static::$wordCache['plural'][$word] = preg_replace($rule, $replacement, $word);
				return static::$wordCache['plural'][$word];
			}
		}

		//If nothing matches give the word back unchanged.
		return $word;
	}

	/**
	 * Change a word back to singular form.
	 * @param $word
	 * @return mixed
	 */
	public static function singularize($word)
	{
		//Check for cached conversion
		if (isset(static::$wordCache['singular'][$word]))
		{
			return static::$wordCache['singular'][$word];
		}

		//Check for an irregular conversion
		if (($key = array_search($word,static::$wordCache['irregular'])) !== false)
		{
			static::$wordCache['singular'][$word] = $key;
			return static::$wordCache['singular'][$word];
		}

		//Check for unchanged words
		foreach (static::$unchangedRules as $rule)
		{
			if (preg_match($rule, $word))
			{
				static::$wordCache['singular'][$word] = $word;
				return $word;
			}
		}

		//Check pluralization rules
		foreach (static::$singularRules as $rule => $replacement)
		{
			if (preg_match($rule, $word))
			{
				static::$wordCache['singular'][$word] = preg_replace($rule, $replacement, $word);
				return static::$wordCache['singular'][$word];
			}
		}

		//If nothing matches give the word back unchanged.
		return $word;
	}
}

?>