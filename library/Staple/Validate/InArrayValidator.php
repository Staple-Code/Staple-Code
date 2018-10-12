<?php
/** 
 * Validates that the supplied value is within an array of valid values.
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

class InArrayValidator extends BaseValidator
{
	const DEFAULT_ERROR = 'Supplied data not in accepted list of values.';
	/**
	 * Valid array values.
	 * @var array
	 */
	protected $arrayValues = array();

	/**
	 * Supply an array to the constructor to define valid options.
	 * @param array $values
	 * @param string $userMessage
	 */
	function __construct($userMessage = NULL, array $values = [])
	{
		$this->arrayValues = $values;
		parent::__construct($userMessage);
	}

    /**
     * Factory function to create objects.
     * @param array $values
     * @param string $userMessage
     * @return BaseValidator
     */
    public static function create(string $userMessage = NULL, $values = []): IValidator
    {
        return new static($userMessage, $values);
    }
	
	/**
	 * Add a new value to the valid array list.
	 * @param mixed $value
     * @return InArrayValidator
	 */
	public function addValue($value): InArrayValidator
	{
		if(is_array($value))
		{
			$this->arrayValues = array_merge($this->arrayValues,$value);
		}
		else
		{
			$this->arrayValues[] = $value;
		}

		return $this;
	}

	/**
	 * Check that the supplied data exists as a value in the array;
	 * @param mixed $data
	 * @return bool
	 */
	public function check($data): bool
	{
		if(in_array($data, $this->arrayValues, false) === true)
		{
			return true;
		}
		else
		{
			$this->addError();
		}
		return false;
	}
}