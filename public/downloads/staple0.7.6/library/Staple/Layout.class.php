<?php

/** 
 * 
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
class Staple_Layout
{
	const DOC_HTML4_TRANS = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	const DOC_HTML4_STRICT = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
	const DOC_HTML5 = '<!DOCTYPE HTML>';
	const DOC_XHTML_TRANS = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	const DOC_XHTML_STRICT = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	
	/**
	 * The name/filename of the layout.
	 * @var string
	 */
	protected $name;
	/**
	 * Array of Script includes to add to the page source
	 * @var array
	 */
	protected $scripts = array();
	/**
	 * Array of Script Blocks to write to the page source
	 * @var array
	 */
	protected $scriptBlocks = array();
	/**
	 * An array of Stylesheets to add to the page.
	 * @var array
	 */
	protected $styles = array();
	/**
	 * An array of META information
	 * @var array[string]
	 */
	protected $metas = array();
	/**
	 * The view object
	 * @var Staple_View
	 */
	protected $view;
	/**
	 * The buffered script output from the controller.
	 * @var string
	 */
	protected $buffer;
	/**
	 * The page title.
	 * @var string
	 */
	public $title;
	/**
	 * Stores the DocType for the layout
	 * @var unknown_type
	 */
	public $doctype;
	/**
	 * The dynamic datastore.
	 * @var array
	 */
	protected $_store = array();

	public function __construct($name = NULL, $doctype = "html5")
	{
		if(isset($name))
		{
			$this->setName($name);
		}
		
		switch($doctype)
		{
			case 'html4_trans':
			case 'html4':
				$this->doctype = self::DOC_HTML4_TRANS;
				break;
			case 'html4_strict':
				$this->doctype = self::DOC_HTML4_STRICT;
				break;
			case 'xhtml_trans':
			case 'xhtml':
				$this->doctype = self::DOC_XHTML_TRANS;
				break;
			case 'xhtml_strict':
				$this->doctype = self::DOC_XHTML_STRICT;
				break;
			default: 
				$this->doctype = self::DOC_HTML5;
		}
		
		$settings = Staple_Config::get('layout');
		
		if(is_array($settings))
		{
			//Add the default scripts to the layout
			if(array_key_exists('scripts', $settings))
			{
				if(is_array($settings['scripts']))
				{
					foreach($settings['scripts'] as $src)
					{
						$this->addScript($src);
					}
				}
				else
				{
					$this->addScript($settings['scripts']);
				}
			}
			
			//Add the default styles to the layout
			if(array_key_exists('styles', $settings))
			{
				if(is_array($settings['styles']))
				{
					foreach($settings['styles'] as $href)
					{
						$this->addStylesheet($href);
					}
				}
				else
				{
					$this->layout->addStylesheet($settings['styles']);
				}
			}
			
			//Add the default metas to the layout
			if(array_key_exists('meta_description', $settings))
			{
				$this->setMetas('description', $settings['meta_description']);
			}
			if(array_key_exists('meta_keywords', $settings))
			{
				$this->setMetas('keywords', $settings['meta_keywords']);
			}
		}
	}
	
	/**
	 * Overloaded __set allows for dynamic addition of properties.
	 * @param string | int $key
	 * @param mixed $value
	 */
	public function __set($key,$value)
	{
		$this->_store[$key] = $value;
	}
	
	/**
	 * Retrieves a stored property.
	 * @param string | int $key
	 */
	public function __get($key)
	{
		if(array_key_exists($key,$this->_store))
		{
			return $this->_store[$key];
		}
		else
		{
			return NULL;
		}
	}
	
	public function __sleep()
	{
		return array('name','scripts','styles','metas','view','title','doctype');
	}
	
	public function addScript($script,$keepProtocol = false)
	{
		if($keepProtocol != true)
		{
			$script = str_replace(array('http://','https://'), 'proto://', $script);
		}
		if(in_array($script, $this->scripts) === false)
		{
			$this->scripts[] = $script;
		}
		return $this;
	}
	
	public function removeScript($script)
	{
		if(($key = array_search($script, $this->scripts)) !== false)
		{
			unset($this->scripts[$key]);
		}
		return $this;
	}
	
	public function addScriptBlock($script)
	{
		if(!in_array($script, $this->scriptBlocks))
		{
			$this->scriptBlocks[] = $script;
		}
		return $this;
	}
	
	public function addStylesheet($style, $media = 'all')
	{
		if(array_key_exists($media, $this->styles))
		{
			if(is_array($this->styles[$media]))
			{
				if(in_array($style, $this->styles[$media]) === false)
				{
					$this->styles[$media][] = $style;
				}
			}
			else
			{
				$this->styles[$media] = array($style);
			}
		}
		else
		{
			$this->styles[$media] = array($style);
		}
		return $this;
	}
	
	public function removeStylesheet($style, $media)
	{
		if(($key = array_search($style, $this->styles[$media])) !== false)
		{
			unset($this->styles[$media][$key]);
		}
		return $this;
	}
	
	public function addMeta($name, $content)
	{
		if(!(array_key_exists($name, $this->metas)))
		{
			$this->metas[$name] = $content;
		}
		return $this;
	}
	
	public function removeMeta($name)
	{
		if(array_key_exists($name, $this->metas))
		{
			unset($this->metas[$name]);
		}
		return $this;
	}
	
	/**
	 * @return the $metas
	 */
	public function getMetas($name)
	{
		if(array_key_exists($name, $this->metas))
		{
			return $this->metas[$name];
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * @param field_type $metas
	 */
	public function setMetas($name,$content)
	{
		$this->metas[$name] = $content;
		return $this;
	}

	public function setView(Staple_View $view)
	{
		$this->view = $view;
		return $this;
	}
	
	/**
	 * @return the $title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return the $name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the Layout Name
	 * @param string $name
	 * @throws Exception
	 */
	public function setName($name)
	{
		if(ctype_alnum(str_replace('_','',$name)))
		{
			$this->name = $name;
		}
		else
		{
			throw new Exception('Invalid Layout', Staple_Error::APPLICATION_ERROR);
		}
		return $this;
	}

	/*---------------------------------------Helper Fuctions---------------------------------------*/
	/**
	 * 
	 * If an array is supplied, a link is created to a controller/action. If a string is
	 * supplied, a file link is specified.
	 * @param string | array $link
	 * @param array $get
	 */
	public function link($link,array $get = array())
	{
		return Staple_Link::get($link,$get);
	}
	
	/**
	 * @see Staple_View::escape()
	 * @param string $estring
	 * @param boolean $strip
	 */
	public static function escape($estring, $strip = false)
	{
		return Staple_View::escape($estring,$strip);
	}
	
	/*---------------------------------------Builder Fuctions---------------------------------------*/
	
	public function doctype()
	{
		echo $this->doctype."\r\n";
	}
	
	/**
	 * Prints the stylesheets to the document
	 */
	public function styles()
	{
		foreach($this->styles as $media=>$styles)
		{
			foreach($styles as $href)
			{
				switch($this->doctype)
				{
					case self::DOC_XHTML_TRANS:
					case self::DOC_XHTML_STRICT:
						echo "<link href=\"".htmlentities($href)."\" rel=\"stylesheet\" type=\"text/css\" media=\"".htmlentities($media)."\" />\n";
						break;
					default:
						echo "<link href=\"".htmlentities($href)."\" rel=\"stylesheet\" type=\"text/css\" media=\"".htmlentities($media)."\">\n";
				}
			}
		}
	}
	
	public function scripts()
	{
		$secure = Staple_Request::isSecure();
		foreach($this->scripts as $src)
		{
			if($secure === true)
			{
				$src = str_replace('proto://','https://', $src);
			}
			else
			{
				$src = str_replace('proto://','http://', $src);
			}
			echo "<script src=\"".htmlentities($src)."\" type=\"text/javascript\"></script>\n";
		}
		foreach($this->scriptBlocks as $sBlock)
		{
			echo "<script type=\"text/javascript\">\n<!--\n";
			echo $sBlock;
			echo "\n-->\n</script>\n";
		}
	}
	
	public function metas()
	{
		foreach($this->metas as $name=>$content)
		{
			switch($this->doctype)
			{
				case self::DOC_XHTML_TRANS:
				case self::DOC_XHTML_STRICT:
					echo "<meta name=\"".htmlentities($name)."\" content=\"".htmlentities($content)."\" />\n";
					break;
				default:
					echo "<meta name=\"".htmlentities($name)."\" content=\"".htmlentities($content)."\">\n";
			}
		}
	}
	
	public function content()
	{
		if(isset($this->buffer))
		{
			echo $this->buffer;
		}
		if($this->view instanceof Staple_View)
		{
			$this->view->build();
		}
	}
	
	public function build($buffer = NULL)
	{
		if(isset($this->name))
		{
			if(isset($buffer))
			{
				$this->buffer = $buffer;
			}
			if(file_exists(LAYOUT_ROOT.$this->name.'.phtml'))
			{
				include LAYOUT_ROOT.$this->name.'.phtml';
			}
			else
			{
				throw new Exception("Unable to locate layout", Staple_Error::APPLICATION_ERROR);
			}
		}
		else
		{
			throw new Exception("Attemped to build unknown layout.", Staple_Error::APPLICATION_ERROR);
		}
	}
}

?>