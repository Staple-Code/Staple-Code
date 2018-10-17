<?php
/** 
 * Validates a numeric value is between the min and the max.
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

class BetweenValidator extends BaseValidator
{
	const DEFAULT_ERROR = 'Field is not between minimum and maximum values.';

    /**
     * Minimum Value.
     * @var int
     */
	protected $min = 0;
    /**
     * Maximum Value.
     * @var int
     */
	protected $max;
	
	/**
	 * Mathematical between function. Requires a maximum value and a minimum value.
	 * Comparison occurs with integer math.
	 * 
	 * @param int $limit1
	 * @param int $limit2
	 * @param string $userMessage
	 */
	public function __construct($limit1, $limit2, $userMessage = NULL)
	{
		$this->min = (int)$limit1;
		if(isset($limit2))
		{
			if($limit2 >= $limit1)
			{
				$this->max = (int)$limit2;
			}
			else 
			{
				$this->min = (int)$limit2;
				$this->max = (int)$limit1;
			}
		}
		parent::__construct($userMessage);
	}
	
	/**
	 * @return int $min
	 */
	public function getMin(): int
	{
		return $this->min;
	}

	/**
	 * @return int $max
	 */
	public function getMax(): int
	{
		return $this->max;
	}

	/**
	 * @param int $min
	 * @return $this
	 */
	public function setMin($min): BetweenValidator
	{
		$this->min = $min;
		return $this;
	}

	/**
	 * @param int $max
	 * @return $this
	 */
	public function setMax($max): BetweenValidator
	{
		$this->max = $max;
		return $this;
	}

	/**
	 * Check for Data Length Validity.
	 * @param mixed $data
	 * @return boolean
	 */
	public function check($data): bool
	{
		$data = (int)$data;
		if($data <= $this->max && $data >= $this->min)
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