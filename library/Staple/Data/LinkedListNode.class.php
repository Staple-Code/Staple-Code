<?php
/** 
 * Linked List Data Nodes
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
class Staple_Data_LinkedListNode
{
	/**
	 * The data contained in the node
	 * @var mixed
	 */
	public $data;
	/**
	 * Pointer to the next node
	 * @var Staple_Data_LinkedListNode
	 */
	public $next;
	
	/**
	 * Constructor to create data and link the node.
	 * @param mixed $data
	 * @param Staple_Data_LinkedListNode $next
	 */
	public function __construct($data,$next = null)
	{
		//Set the data
		$this->setData($data);
		
		//Set the next node link
		if(isset($next))
		{
			$this->setNext($next);
		}
	}
	/**
	 * @return the $data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return Staple_Data_LinkedListNode $next
	 */
	public function getNext()
	{
		return $this->next;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * @param Staple_Data_LinkedListNode $next
	 */
	public function setNext(Staple_Data_LinkedListNode $next)
	{
		$this->next = $next;
		return $this;
	}	
}