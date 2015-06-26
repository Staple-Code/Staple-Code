<?php
/** 
 * A Linked List data structure.
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
namespace Staple\Data;

class LinkedListNodeDouble extends LinkedListNode
{
	/**
	 * The previous linked node.
	 * @var Staple_Data_LinkedListNodeDouble
	 */
	public $prev;
	
	/**
	 * Constructor
	 * @param mixed $data
	 * @param Staple_Data_LinkedListNodeDouble $next
	 * @param Staple_Data_LinkedListNodeDouble $prev
	 */
	public function __construct($data, LinkedListNodeDouble $next = NULL, LinkedListNodeDouble $prev = NULL)
	{
		//Call the parent constructor
		parent::__construct($data,$next);
		
		//Set the previous node pointer
		if(isset($prev))
		{
			$this->setPrev($prev);
		}
	}
	/**
	 * @return the $prev
	 */
	public function getPrev()
	{
		return $this->prev;
	}

	/**
	 * @param Staple_Data_LinkedListNodeDouble $prev
	 */
	public function setPrev($prev)
	{
		$this->prev = $prev;
		return $this;
	}
}