<?php

/** 
 * The base abstraction class for form field filters. All filters must be inherited from this
 * class. A filter is designed to remove or modify the contents of a field before it is stored
 * in the form. Filters are processed in the order that they are added to a form field.
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
namespace Staple\Form;

abstract class FieldFilter
{
	/**
	 * Filters a field and returns the filtered value;
	 * @return string
	 */
	abstract public function filter($text);
	
	/**
	 * Returns a string value for the filter name. This prevents filters from being added to a field
	 * multiple times.
	 */
	abstract public function getName();
	
	/**
	 * Static factory creation method.
	 */
	public static function Create()
	{
		return new static();
	}
}