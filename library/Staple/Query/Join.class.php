<?php

/** 
 * A class used to join two SQL tables together.
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
namespace Staple\Query;

class Join
{
	const JOIN = "JOIN";
	const LEFT = "LEFT OUTER JOIN";
	const RIGHT = "RIGHT OUTER JOIN";
	const INNER = "INNER JOIN";
	const CROSS = "CROSS JOIN";
	const STRAIGHT = "STRAIGHT_JOIN";
	const NATURAL = "NATURAL JOIN";
	const NATURAL_LEFT = "NATURAL LEFT JOIN";
	const NATURAL_RIGHT = "NATURAL RIGHT JOIN";
	
	/**
	 * Left Table
	 * @var string
	 */
	protected $lefttable;
	/**
	 * Left Join Column(s)
	 * @var string | array
	 */
	protected $leftcolumn;
	/**
	 * Right Table - The table that is being added to the left table.
	 * @var string
	 */
	protected $table;
	/**
	 * Right Column(s)
	 * @var string | array
	 */
	protected $rightcolumn;
	/**
	 * Join Condition
	 * @var string
	 */
	protected $condition;
	/**
	 * Join Type
	 * @var string
	 */
	protected $type;
	
	/**
	 * Default Constructor
	 * @param string $type
	 * @param string $table
	 * @param string $condition
	 * @param string $lefttable
	 * @param string $leftcolumn
	 * @param string $rightcolumn
	 */
	public function __construct($type = self::INNER, $table = NULL, $condition = NULL, $lefttable = NULL, $leftcolumn = NULL, $rightcolumn = NULL)
	{
		$this->setType($type);
		if(isset($table))
		{
			$this->setTable($table);
		}
		if(isset($condition))
		{
			$this->setCondition($condition);
		}
	}
	
	public function __toString()
	{
		return $this->build();
	}
	
	/**
	 * @return the $lefttable
	 */
	public function getLefttable()
	{
		return $this->lefttable;
	}

	/**
	 * @return the $leftcolumn
	 */
	public function getLeftcolumn()
	{
		return $this->leftcolumn;
	}

	/**
	 * @return the $table
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @return the $rightcolumn
	 */
	public function getRightcolumn()
	{
		return $this->rightcolumn;
	}

	/**
	 * @return the $condition
	 */
	public function getCondition()
	{
		return $this->condition;
	}

	/**
	 * @return the $type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $lefttable
	 */
	public function setLefttable($lefttable)
	{
		$this->lefttable = $lefttable;
		return $this;
	}

	/**
	 * @param string $leftcolumn
	 */
	public function setLeftcolumn($leftcolumn)
	{
		$this->leftcolumn = $leftcolumn;
		return $this;
	}

	/**
	 * @param string $table
	 */
	public function setTable($table)
	{
		$this->table = $table;
		return $this;
	}

	/**
	 * @param string $rightcolumn
	 */
	public function setRightcolumn($rightcolumn)
	{
		$this->rightcolumn = $rightcolumn;
		return $this;
	}

	/**
	 * @param string $condition
	 */
	public function setCondition($condition)
	{
		$this->condition = $condition;
		return $this;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	public function build()
	{
		$join = $this->getType().' '.$this->table;
		if(isset($this->condition))
		{
			$join .= ' ON '.$this->condition;
		}
		elseif(isset($this->lefttable) && isset($this->leftcolumn) && isset($this->rightcolumn))
		{
			$join .= ' ON '.$this->lefttable.'.'.$this->leftcolumn.'='.$this->table.'.'.$this->rightcolumn;
		}
		return $join;
	}
	
	public static function inner($table, $condition)
	{
		return new static(self::INNER,$table,$condition);
	}
	
	public static function left($table,$condition)
	{
		return new static(self::LEFT,$table,$condition);
	}
}

?>