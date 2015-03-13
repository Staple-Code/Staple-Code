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
 * 
 */
namespace Staple;

class Pager
{
	/**
	 * Total number of items in the paged set.
	 * @var int
	 */
	protected $total;
	/**
	 * The current page of the item set.
	 * @var int
	 */
	protected $page = 1;
	/**
	 * Number of items listed on each page.
	 * @var int
	 */
	protected $itemsPerPage = 10;
	/**
	 * Number of pages to list before the current page.
	 * @var int
	 */
	protected $pagesBeforeCurrent;
	/**
	 * Number of pages to list after the current page.
	 * @var int
	 */
	protected $pagesAfterCurrent;
	/**
	 * An array that contains additional variables for the paging function.
	 * @var array[string]
	 */
	protected $pageVariables = array();
	/**
	 * A switch to disable the item amount selector field.
	 * @var bool
	 */
	protected $displayItemAmountSelector = true;
	/**
	 * The default item amount selections.
	 * @var array[int]
	 */
	protected $itemAmountSelections = array(5,10,20,50,100);
	/**
	 * The constructor loads values into the pager.
	 * @param int $itemsPerPage
	 * @param int $total
	 * @param int $currentPage
	 * @param int $pageBuffer
	 */
	function __construct($itemsPerPage = NULL, $total = NULL, $currentPage = NULL, $pageBuffer = NULL)
	{
		if(isset($itemsPerPage))
		{
			$this->setItemsPerPage($itemsPerPage);
		}
		if(isset($total))
		{
			$this->setTotal($total);
		}
		if(isset($currentPage))
		{
			$this->setPage($currentPage);
		}
		if(isset($pageBuffer))
		{
			$this->setPageBuffer($pageBuffer);
		}
	}
	/**
	 * Returns the displayPaging() function.
	 * @return string
	 */
	public function __toString()
	{
		return $this->displayPaging();
	}
	
	/**
	 * Returns the item number for the starting item in the result set. Useful for filling in the LIMIT clause in MySQL.
	 */
	public function getStartingItem()
	{
		$start = (($this->page - 1) * $this->itemsPerPage);
		if($start > 0)
		{
			return $start;
		} 
		else 
		{
			return 0;
		}
	}
	
	/**
	 * An Alias of self::getLastPage() 
	 */
	public function getNumberOfPages()
	{
		return $this->getLastPage();
	}
	
	/**
	 * Returns the ending page of the set.
	 * @return int
	 */
	public function getLastPage()
	{
		if(($this->total % $this->itemsPerPage) == 0)
		{
			return (int)($this->total / $this->itemsPerPage);
		}
		else
		{
			return (int)ceil($this->total / $this->itemsPerPage);
		}
	}
	
	/**
	 * Returns an array with the page numbers.
	 */
	public function getPages()
	{
		$start = 1;
		if(isset($this->pagesBeforeCurrent))
		{
			if(($this->page - $this->pagesBeforeCurrent) > 0)
			{
				$start = $this->page - $this->pagesBeforeCurrent;
			}
		}
		$end = $this->getNumberOfPages();
		if(isset($this->pagesAfterCurrent))
		{	
			if(($this->page + $this->pagesAfterCurrent) < $end)
			{
				$end = $this->page + $this->pagesAfterCurrent;
			}
		}
		
		$pages = array();
		for($i = $start; $i <= $end; $i++)
		{
			$pages[] = $i;
		}
		
		return $pages;
	}
	
	//---------------------------------------------Getters and Setters---------------------------------------------
	/**
	 * @return int $total
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * @param int $total
	 * @return $this
	 */
	public function setTotal($total)
	{
		$this->total = $total;
		if($this->getStartingItem() > $total)
		{
			$this->setPage($this->getLastPage());
		}
		return $this;
	}

	/**
	 * Alias of getPage()
	 * @see Staple_Pager::getPage()
	 */
	public function getCurrentPage()
	{
		return $this->getPage();
	}
	
	/**
	 * @return int $page
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * @param int $page
	 * @return $this
	 */
	public function setPage($page)
	{
		if(isset($this->total) && ($page*$this->itemsPerPage) > $this->total)
		{
			$this->page = $this->getLastPage();
		}
		elseif ($page <= 0)
		{
			$this->page = 1;
		}
		else
		{
			$this->page = $page;
		}
		return $this;
	}

	/**
	 * @return int $itemsPerPage
	 */
	public function getItemsPerPage()
	{
		return (int)$this->itemsPerPage;
	}

	/**
	 * @param int $itemsPerPage
	 * @return $this
	 */
	public function setItemsPerPage($itemsPerPage)
	{
		//@todo recalculate the current page.
		$this->itemsPerPage = $itemsPerPage;
		return $this;
	}
	/**
	 * @return bool $displayItemAmountSelector
	 */
	public function getDisplayItemAmountSelector()
	{
		return $this->displayItemAmountSelector;
	}

	/**
	 * @return array[int] $itemAmountSelections
	 */
	public function getItemAmountSelections()
	{
		return $this->itemAmountSelections;
	}

	/**
	 * @param boolean $displayItemAmountSelector
	 * @return $this
	 */
	public function setDisplayItemAmountSelector($displayItemAmountSelector)
	{
		$this->displayItemAmountSelector = (bool)$displayItemAmountSelector;
		return $this;
	}

	/**
	 * @param array[int]  $itemAmountSelections
	 * @return $this
	 */
	public function setItemAmountSelections(array $itemAmountSelections)
	{
		$this->itemAmountSelections = array();
		foreach ($itemAmountSelections as $value)
		{
			if(is_int($value))
			{
				$this->itemAmountSelections[] = $value;
			}
		}
		return $this;
	}
	/**
	 * Add a single entry to the selection list.
	 * @param int $amount
	 * @return $this
	 */
	public function addItemAmountSelection($amount)
	{
		$this->itemAmountSelections[] = (int)$amount;
		sort($this->itemAmountSelections);
		return $this;
	}

	/**
	 * @return int $pagesBeforeCurrent
	 */
	public function getPagesBeforeCurrent()
	{
		return $this->pagesBeforeCurrent;
	}

	/**
	 * @return int $pagesAfterCurrent
	 */
	public function getPagesAfterCurrent()
	{
		return $this->pagesAfterCurrent;
	}

	/**
	 * @param int $pagesBeforeCurrent
	 * @return $this
	 */
	public function setPagesBeforeCurrent($pagesBeforeCurrent)
	{
		$this->pagesBeforeCurrent = $pagesBeforeCurrent;
		return $this;
	}

	/**
	 * @param int $pagesAfterCurrent
	 * @return $this
	 */
	public function setPagesAfterCurrent($pagesAfterCurrent)
	{
		$this->pagesAfterCurrent = $pagesAfterCurrent;
		return $this;
	}
	
	/**
	 * Sets the $pagesBeforeCurrent and $pagesAfterCurrent to the same value.
	 * @param int $pages
	 * @return $this
	 */
	public function setPageBuffer($pages)
	{
		$this->pagesAfterCurrent = $pages;
		$this->pagesBeforeCurrent = $pages;
		return $this;
	}
	
	/**
	 * This function allows the user to add additional variables to the GET string of the page links.
	 * The array should be associative with it formated such that variabelName=>value.
	 * @param array $vars
	 * @return Pager
	 */
	public function setVariables(array $vars)
	{
		if(array_key_exists('items', $vars))
		{
			unset($vars['items']);
		}
		$this->pageVariables = $vars;
		return $this;
	}
	/**
	 * Add a single variable to the page GET string
	 * @param string $varname
	 * @param string $value
	 * @return $this
	 */
	public function addVariable($varname,$value)
	{
		$this->pageVariables[$varname] = $value;
		return $this;
	}
	/**
	 * Remove a single variable from the GET string.
	 * @param string $varname
	 * @return bool
	 */
	public function removeVariable($varname)
	{
		if(array_key_exists($varname, $this->pageVariables))
		{
			unset($this->pageVariables[$varname]);
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	 * This function is a temporary fix until the changes are completed to Staple_Route.
	 * @param string $action
	 * @deprecated
	 * @return string
	 */
	public function displayPaging($action = NULL)
	{
		//Setup Link Variables
		$linkVars = array_merge($_GET,$this->pageVariables);
		
		//These two variables would be overridden anyway.
		if(array_key_exists('items', $linkVars)) unset($linkVars['items']);
		if(array_key_exists('page', $linkVars)) unset($linkVars['page']);
		
		//Set a default link action location if none is submitted.
		if($action == NULL)
		{
			$action = Main::get()->getRoute();
		}
		$buffer = "<div class=\"staple_pager\">\n<div class=\"staple_pager_pages\">\nPage: ";
		$pages = $this->getPages();
		if(count($pages) > 1)
		{
			if($this->getCurrentPage() == 1)
			{
				$buffer .= ' &lt;&lt; - &lt; ';
			}
			elseif($this->getCurrentPage() > 1) 
			{
				$buffer .= '<a href="'.Link::get($action,array_merge($linkVars,array('page'=>1))).'">&lt;&lt;</a> - ';
				$buffer .= '<a href="'.Link::get($action,array_merge($linkVars,array('page'=>($this->getCurrentPage()-1)))).'">&lt;</a> ';
			}
			if($pages[0] != 1)
			{
				$buffer .= '... ';
			}
			foreach($pages as $page)
			{
				if($this->getCurrentPage() == $page)
				{
					$buffer .= '<span class="currentpage">'.((int)$page).'</span> ';
				}
				else 
				{
					$buffer .= '<a href="'.Link::get($action,array_merge($linkVars,array('page'=>(int)$page))).'">'.((int)$page).'</a> ';
				}
			}
			if($pages[count($pages)-1] != $this->getNumberOfPages())
			{
				$buffer .= '... ';
			}
			if($this->getCurrentPage() == $this->getNumberOfPages())
			{
				$buffer .= ' &gt; - &gt;&gt; ';
			}
			else
			{
				$buffer .= '<a href="'.Link::get($action,array_merge($linkVars,array('page'=>($this->getCurrentPage()+1)))).'">&gt;</a> - '; 
				$buffer .= '<a href="'.Link::get($action,array_merge($linkVars,array('page'=>$this->getNumberOfPages()))).'">&gt;&gt;</a> ';
			}
		}
		else 
		{
			$buffer .= '<< - < 1 > - >>';
		}
		$buffer .= "</div>\n";
		if($this->getDisplayItemAmountSelector() === true)
		{
			
			$buffer .= "<div class=\"staple_pager_items\">\n";
			$buffer .= 'Items Per Page: <select onChange="window.location=\''.Link::get($action,array_merge($linkVars,array('page'=>1)))."&items='+this.value\">\n";
			foreach($this->getItemAmountSelections() as $value)
			{
				$selected = '';
				if($this->getItemsPerPage() == $value)
				{
					$selected = ' selected';
				}
				$buffer .= '<option value="'.$value.'"'.$selected.'>'.$value."</option>\n";
			}
			$buffer .= "</select>\n";
			$buffer .= "</div>\n";
		}
		$buffer .= "</div>\n";
		return $buffer;
	}
}
?>