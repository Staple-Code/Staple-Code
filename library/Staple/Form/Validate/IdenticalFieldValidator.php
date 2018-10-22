<?php
/** 
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
namespace Staple\Form\Validate;

use Staple\Form\IFieldElement;

class IdenticalFieldValidator extends BaseFieldValidator
{
	const DEFAULT_ERROR = 'Data is not equal.';

    /**
     * Boolean to use a strict comparison or not.
     * @var bool
     */
	protected $strict = false;

	/**
	 * @param IFieldElement $field
	 * @param bool $strict
	 * @param string $userMessage
	 */
	public function __construct(IFieldElement $field = NULL, $strict = false, $userMessage = NULL)
	{
		if(isset($field))
		{
			$this->setField($field);
		}
		$this->strict = (bool)$strict;
		parent::__construct($userMessage);
	}

	/**
	 * @param  mixed $data
	 * @return  bool
	 * @see Staple_Form_Validator::check()
	 */
	public function check($data): bool
	{
		if($this->strict === true)
		{
			if($this->field->getValue() === $data)
			{
				return true;
			}
			else
			{
				$this->addError();
			}
		}
		else
		{
			if($this->field->getValue() == $data)
			{
				return true;
			}
			else
			{
				$this->addError();
			}
		}
		return false;
	}
	
	/**
	 * @return bool $strict
	 */
	public function getStrict()
	{
		return $this->strict;
	}

	/**
	 * @param bool $strict
	 * @return $this
	 */
	public function setStrict(bool $strict): IdenticalFieldValidator
	{
		$this->strict = $strict;
		return $this;
	}
}