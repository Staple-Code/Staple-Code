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

class BetweenFloatValidator extends BaseValidator
{
	const DEFAULT_ERROR = 'Field is not between minimum and maximum values.';
	/**
	 * Minimum Value
	 * @var float
	 */
	protected $min = 0;
	/**
	 * Maximum Value
	 * @var float
	 */
	protected $max;
	
	/**
	 * Mathematical between function. Requires a maximum value and a minimum value.
	 * Comparison occurs with float math.
	 * 
	 * @param float $limit1
	 * @param float $limit2
	 * @param string $userMessage
	 */
	public function __construct($limit1, $limit2, $userMessage = NULL)
	{
		$this->min = (float)$limit1;
		if(isset($limit2))
		{
			if($limit2 >= $limit1)
			{
				$this->max = (float)$limit2;
			}
			else 
			{
				$this->min = (float)$limit2;
				$this->max = (float)$limit1;
			}
		}
		parent::__construct($userMessage);
	}
	
	/**
	 * @return float $min
	 */
	public function getMin(): float
	{
		return $this->min;
	}

	/**
	 * @return float $max
	 */
	public function getMax(): float
	{
		return $this->max;
	}

	/**
	 * @param float $min
	 * @return $this
	 */
	public function setMin(float $min): BetweenFloatValidator
	{
		$this->min = $min;
		return $this;
	}

	/**
	 * @param float $max
	 * @return $this
	 */
	public function setMax(float $max): BetweenFloatValidator
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
		$data = (float)$data;
		if($data <= ($this->max+0.0625) && $data >= $this->min)			//+0.06256 Binary Float fix
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