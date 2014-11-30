<?php
/** 
 * A Linked List data structure.
 * The structure allows iteration and array access. Note: when unsetting an array key, 
 * the list is compressed and all subsequent keys are reset.
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

class Staple_Data_DoubleLinkedList implements Iterator, Countable, ArrayAccess
{
	/**
	 * Pointer to the starting list node
	 * @var Staple_Data_LinkedListNodeDouble
	 */
	protected $first;
	/**
	 * Pointer to the current list node
	 * @var Staple_Data_LinkedListNodeDouble
	 */
	protected $current;
	/**
	 * Pointer to the ending list node
	 * @var Staple_Data_LinkedListNodeDouble
	 */
	protected $last;
	/**
	 * Size of the list
	 * @var int
	 */
	protected $size;
	/**
	 * Name of the list. (optional)
	 * @var string
	 */
	protected $name;
	
	/**
	 * Constructs the list and allows an optional list name to be set.
	 * @param unknown_type $name
	 */
	public function __construct($name = NULL)
	{
		//Sets the first and last pointers to null making a blank list.
		$this->first = $this->last = $this->current = null;
		$this->size = 0;
		
		//Set the Name
		if(isset($name))
		{
			$this->setName($name);
		}
	}
	
	/**
	 * Calls the getListString function on string conversion.
	 */
	public function __toString()
	{
		return $this->getListString();
	}
	
	/**
	 * Returns the list size.
	 * @return int
	 */
	public function count()
	{
		return $this->getSize();
	}

	/**
	 * Iteration. Retrieves the data for the current item.
	 * @return mixed
	 */
	public function current()
	{
		if(!is_null($this->current))
		{
			return $this->current->getData();
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Iteration. Gets the key number for the current item in the list.
	 * @return int
	 */
	public function key()
	{
		return $this->findKey($this->current);
	}

	/**
	 * Iteration. Forwards the internal pointer to the next item on the list.
	 */
	public function next()
	{
		if(!is_null($this->current))
		{
			$this->current = $this->current->getNext();
		}
	}

	/**
	 * Iteration. Rewinds the internal pointer to the first item in the list.
	 */
	public function rewind()
	{
		$this->current = $this->first;
	}

	/**
	 * Iteration. Checks that the current item pointed to is valid.
	 */
	public function valid()
	{
		return !is_null($this->current);
	}

	/**
	 * Array Access. Checks for a valid offset.
	 */
	public function offsetExists($offset)
	{
		return !is_null($this->findItemByKey($offset));
	}

	/**
	 * Array Access. Returns the data for a specified offset.
	 */
	public function offsetGet($offset)
	{
		return $this->findItemByKey($offset)->getData();
	}

	/**
	 * Array Access. Set the data for a specified offset.
	 */
	public function offsetSet($offset, $value)
	{
		$item = $this->findItemByKey($offset);
		if($item instanceof Staple_Data_LinkedListNodeDouble)
		{
			$item->setData($value);
		}
	}

	/**
	 * Array Access. Removes the node for a specified offset
	 */
	public function offsetUnset($offset)
	{
		$prev = $this->findItemByKey($offset-1);
		$item = $this->findItemByKey($offset);
		$prev->setNext($item->getNext());
	}

	/**
	 * Alias of addBack()
	 * @param mixed $data
	 */
	public function add($data)
	{
		return $this->addBack($data);
	}
	
	/**
	 * Add a node to the beginning of the list.
	 * @param mixed $data
	 */
	public function addBefore($data, Staple_Data_LinkedListNodeDouble $beforeNode)
	{
		if($this->is_empty())
		{
			throw new Exception('Cannot add a node before another in a blank list.');
		}
		else
		{
			//Create the new node and insert it into the list.
			$new = new Staple_Data_LinkedListNodeDouble($data,$beforeNode, $beforeNode->prev);
			
			//Set the Surrounding Nodes
			if($beforeNode->prev != NULL)
			{
				$beforeNode->prev->setNext($new);
			}
			else
			{
				//Set the Current Node to the beginning of the list.
				$this->current = $this->first = $new;
			}
			$beforeNode->prev = $new;
		}
	
		//Increase List Size
		$this->size++;
		
		return $this;
	}
	
	/**
	 * Add a node to the beginning of the list.
	 * @param mixed $data
	 */
	public function addFront($data)
	{
		if($this->is_empty())
		{
			$this->first = $this->last = new Staple_Data_LinkedListNodeDouble($data);
		}
		else
		{
			$new = new Staple_Data_LinkedListNodeDouble($data,$this->first);
			$this->first = $new;
		}
		
		//Increase List Size
		$this->size++;
		
		//Set the Current Node to the beginning of the list.
		$this->current = $this->first;
		
		return $this;
	}
	
	/**
	 * Add a node to the end of the list
	 * @param mixed $data
	 */
	public function addBack($data)
	{
		//Adds a node to the end of the list.
		if($this->is_empty())
		{
			$this->first = $this->last = new Staple_Data_LinkedListNodeDouble($data);
		}
		else
		{
			$new = new Staple_Data_LinkedListNodeDouble($data);
			$new->prev = $this->last;
			$this->last->next = $new;
			$this->last = $new;
		}
		//Increase List Size
		$this->size++;
		
		//Set the Current Node to the end of the list.
		$this->current = $this->last;
		
		return $this;
	}
	
	/**
	 * Alias of removeBack()
	 * @return Staple_Data_LinkedListNodeDouble | false
	 */
	public function remove()
	{
		return $this->removeBack();
	}
	
	/**
	 * Removes the specified node and returns it's data
	 * @param Staple_Data_LinkedListNodeDouble $node
	 * @return boolean|Ambigous <the, mixed>
	 */
	public function removeNode(Staple_Data_LinkedListNodeDouble $node)
	{
		if($this->is_empty())
		{
			$this->size = 0;
			return false;
		}
		elseif($node == $this->first && $node == $this->last)
		{
			$this->first = $this->last = $this->current = null;
			$this->size = 0;
			return $node->getData();
		}
		else
		{
			$next = $node->getNext();
			$prev = $node->getPrev();
			
			if($this->getCurrentNode() == $node && $node->prev != NULL)
			{
				//Set the Current Node to the previous node in the list.
				$this->current = $node->prev;
				
				//If there is no next node end the list with the previous node.
				if($node->next == NULL)
				{
					$this->last = $node->prev;
				}
			}
			else
			{
				//Set the Current Node & First Node to the next node in the list.
				$this->current = $this->first = $node->next;
			}
			
			//Cross-link the next and previous nodes together.
			if($prev != NULL)
			{
				$prev->setNext($next);
				if($next == NULL)
				{
					$this->last = $prev;
				}
				$this->current = $prev;
			}
			if($next != NULL)
			{
				$next->setPrev($prev);
				if($prev == NULL)
				{
					$this->first = $next;
					$this->current = $next;
				}
			}
			
			//Decrease List Size
			$this->size--;
			
			return $node->getData();
		}
	}
	
	/**
	 * Removes a node from the front of the list.
	 * Returns the node data on success or false on an empty list.
	 * @return mixed
	 */
	public function removeFront()
	{
		if($this->is_empty())
		{
			$this->size = 0;
			return false;
		}
		else
		{
			$node = $this->first;
			$this->first = $this->first->next;
			$this->size--;
			
			//Set the Current Node to the beginning of the list.
			$this->current = $this->first;
			
			return $node->getData();
		}
	}
	
	/**
	 * Remove a node from the end of the list.
	 * Returns the node data on removal or false if the list is empty.
	 * @return mixed
	 */
	public function removeBack()
	{
		if($this->is_empty())
		{
			$this->size = 0;
			return false;
		}
		elseif($this->first == $this->last)
		{
			$node = $this->first;
			$this->first = $this->last = $this->current = null;
			$this->size = 0;
			return $node->getData();
		}
		else
		{
			$current = $this->first;
			while($current->next != $this->last)
			{
				$current = $current->next;
			}
			$this->last = $current;
			$node = $current->next;
			$this->last->next = null;
			
			//Decrease List Size
			$this->size--;
			
			//Set the Current Node to the beginning of the list.
			$this->current = $this->last;
			
			return $node->getData();
		}
	}
	
	/**
	 * Sets the optional Name of the list
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	
	/**
	 * Return the current node
	 * @return Staple_Data_LinkedListNodeDouble|NULL
	 */
	public function getCurrentNode()
	{
		if(!is_null($this->current))
		{
			return $this->current;
		}
		else
		{
			return NULL;
		}
	}
	
	/**
	 * Gets the list name
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @return int $size
	 */
	public function getSize()
	{
		return (int)$this->size;
	}

	/**
	 * Alias of getSize()
	 */
	public function length()
	{
		return $this->getSize();
	}
	/**
	 * Returns the list as a string. It converts arrays into a comma separated string.
	 * Throws an exception if objects are encountered.
	 * The Verbose option will also list the name of the list and its size.
	 * @todo convert this to Iterator
	 * @param bool $verbose
	 * @throws Exception
	 */
	public function getListString($verbose = false)
	{
		if($verbose)
			$lstring = "Name: {$this->name} \nSize: {$this->size} \n";
		else
			$lstring = "";
		
		$current = $this->first;
		while($current != null)
		{
			if(is_array($current->data))
				$lstring .= implode(",",$current->data)." \n";
			elseif(is_object($current->data))
				throw new Exception("Cannot interpret object as a string");
			else
				$lstring .= $current->data." \n";
			$current = $current->next;
		}
		return $lstring;
	}
	/**
	 * Returns the list as an array
	 * The Verbose option will also list the name of the list and its size.
	 * Adding ArrayAccess Implementation deprecates this function.
	 * @deprecated
	 * @param bool $verbose
	 */
	public function getListArray($verbose = false)
	{
		$larray = array();
		if($verbose)
		{
			$larray[-2] = $this->name;
			$larray[-1] = $this->size;
		}
		$current = $this->first;
		while($current != null)
		{
			$larray[] = $current->data;
			$current = $current->next;
		}
		return $larray;
	}
	
	/**
	 * Checks the list to see if it is empty.
	 */
	public function is_empty()
	{
		if($this->first == null && $this->last == null)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	 * An internal troubleshooting test to see if the list is sizing correctly
	 * @todo remove this function
	 * @deprecated
	 */
	private function check_size()
	{
		echo "List thinks it is {$this->size} item(s) long.<br>";
		$newSize = 0;
		$current = $this->first;
		while($current != null)
		{
			$newSize++;
			$current = $current->next;
		}
		echo "List currently contains $newSize node(s)";
	}
	
	/**
	 * Returns the key for a specified node
	 * @param Staple_Data_LinkedListNodeDouble $item
	 */
	private function findKey(Staple_Data_LinkedListNodeDouble $item)
	{
		$counter = 0;
		$current = $this->first;
		while($item !== $current && $current instanceof Staple_Data_LinkedListNodeDouble)
		{
			$current = $current->getNext();
			$counter++;
		}
		return $counter;
	}
	
	/**
	 * Returns the node for a specific key
	 * @param int $key
	 * @return Staple_Data_LinkedListNodeDouble
	 */
	private function findItemByKey($key)
	{
		$current = $this->first;
		while($current != NULL && $key > 0)
		{
			$current = $current->getNext();
			$key--;
		}
		return $current;
	}
}