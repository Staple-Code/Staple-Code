<?php
/** 
 * A class for creating the queue (FIFO) data structure.
 * A modification of the Staple_Data_LinkedList class.
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
namespace Staple\Data;

class Queue extends LinkedList
{
	/**
	 * Queue shift function adds to the end of the queue.
	 * Alias of Staple_Data_LinkedList::addBack()
	 * @param mixed $data
	 */
	public function shift($data)
	{
		$this->addBack($data);
		return $this;
	}
	/**
	 * Queue unshift function removes and returns the front element of the list.
	 * Alias of Staple_Data_LinkedList::removeFront()
	 * @return mixed
	 */
	public function unshift()
	{
		return $this->removeFront();
	}
}