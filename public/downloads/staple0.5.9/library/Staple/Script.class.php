<?php

/** 
 * This class is designed to include canned scripts into a website.
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
class Staple_Script
{
	const JQUERY_CURRENT = '1.6.2';
	const JQUERYUI_CURRENT = '1.8.16';
	
	public static function jQuery(Staple_Layout $layout = NULL, $version = NULL)
	{
		if(isset($version))
		{
			$script = 'http://ajax.googleapis.com/ajax/libs/jquery/'.basename($version).'/jquery.min.js';
		}
		else
		{
			$script = 'http://ajax.googleapis.com/ajax/libs/jquery/'.self::JQUERY_CURRENT.'/jquery.min.js';
		}
		if($layout instanceof Staple_Layout)
		{
			$layout->addScript($script);
		}
		return $script;
	}
	
	public static function jQueryUI(Staple_Layout $layout = NULL, $version = NULL)
	{
		if(isset($version))
		{
			$script = 'http://ajax.googleapis.com/ajax/libs/jqueryui/'.basename($version).'/jquery-ui.min.js';
		}
		else
		{
			$script = 'http://ajax.googleapis.com/ajax/libs/jqueryui/'.self::JQUERYUI_CURRENT.'/jquery-ui.min.js';
		}
		if($layout instanceof Staple_Layout)
		{
			$layout->addScript($script);
		}
		return $script;
	}
	
	public static function GoogleMaps(Staple_Layout $layout,$sensor = false)
	{
		$sensor = (bool)$sensor;
		$layout->addScript('http://maps.google.com/maps/api/js?sensor='.$sensor);
	}
}

?>