<?php
/**
 * The autoloader class helps to load controllers and models as well as user objects
 * when they are requested by the application.
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
namespace Staple;

use \Exception;
use Staple\Exception\PageNotFoundException;

class Autoload
{
    const STAPLE_NAMESPACE = 'Staple';
	const CONTROLLER_SUFFIX = 'Controller';
	const FORM_SUFFIX = 'Form';
	const MODEL_SUFFIX = 'Model';
	const PHP_FILE_EXTENSION = '.php';
	const CLASS_FILE_EXTENSION = '.class.php';
	const TRAIT_FILE_EXTENSION = '.trait.php';
	const VIEW_FILE_EXTENSION = '.phtml';
	
	/**
	 * Controller Class Suffix Value
	 * @var string
	 */
	protected $controllerSuffix;
	/**
	 * Array of search directories for the models
	 * @var array[string]
	 */
	protected $controllerSearchDirectories = array();
	/**
	 * Form Class Suffix Value
	 * @var string
	 */
	protected $formSuffix;
	/**
	 * Array of search directories for the models
	 * @var array[string]
	 */
	protected $formSearchDirectories = array();
	/**
	 * Model Class Suffix Value
	 * @var string
	 */
	protected $modelSuffix;
	
	/**
	 * Array of search directories for the models
	 * @var array[string]
	 */
	protected $modelSearchDirectories = array();
	/**
	 * Array of search directories for the views
	 * @var array[string]
	 */
	protected $viewSearchDirectories = array();
	
	/**
	 * Array of search directories for the views
	 * @var array[string]
	 */
	protected $layoutSearchDirectories = array();
	
	/**
	 * Booleon: On loader failure throw an exception
	 * @var bool
	 */
	protected $throwOnFailure = true;
	/**
	 * Automatically loads class files for the application.
	 * @param string $class_name
	 * @throws Exception
	 */
	public function __construct()
	{
		//Add the default controller location
		$this->addControllerSearchDirectory(CONTROLLER_ROOT,false);
		$this->setControllerSuffix(self::CONTROLLER_SUFFIX);
		
		//Add the default form location
		$this->addFormSearchDirectory(FORMS_ROOT,false);
		$this->setFormSuffix(static::FORM_SUFFIX);
		
		//Add the default model location
		$this->addModelSearchDirectory(MODEL_ROOT,false);
		$this->setModelSuffix(static::MODEL_SUFFIX);
		
		//Add the default view location
		$this->addLayoutSearchDirectory(LAYOUT_ROOT,false);
		
		//Add the default view location
		$this->addViewSearchDirectory(VIEW_ROOT,false);
	}
	
	/**
	 * Load a class into the application
	 * @param string $class_name
	 * @throws Exception
	 */
	public function load($class_name)
	{
		//Check for an aliased classname
    	if(!is_null($namespacedClass = Alias::checkAlias($class_name)))					//Look for aliased classes
    	{
    		return $this->loadLibraryClass($namespacedClass, $class_name);
    	}
		elseif(substr($class_name,strlen($class_name)-strlen($this->getControllerSuffix()),strlen($this->getControllerSuffix())) == $this->getControllerSuffix() 
			&& strlen($class_name) != strlen($this->getControllerSuffix()))				//Look for Controllers
		{
			return $this->loadController($class_name);
		}
		elseif(substr($class_name,strlen($class_name)-strlen($this->getModelSuffix()),strlen($this->getModelSuffix())) == $this->getModelSuffix()
			&& strlen($class_name) != strlen($this->getModelSuffix()))					//Look for Models
		{
			return $this->loadModel($class_name);
		}
		elseif(substr($class_name,strlen($class_name)-strlen($this->getFormSuffix()),strlen($this->getFormSuffix())) == $this->getFormSuffix()
			&& strlen($class_name) != strlen($this->getFormSuffix()))					//Look for Forms
		{
			return $this->loadForm($class_name);
		}
		else																			//Look for other elements
		{
			//Correct for a leading \ character
			if(substr($class_name, 0,1) == '\\') $class_name = substr($class_name, 1);
			
			//Split the class into it's namespace components.
			$namespace = explode('\\',$class_name);
			
			if($namespace[0] == static::STAPLE_NAMESPACE)
			{
				return $this->loadLibraryClass($class_name);
			}
			elseif(file_exists(ELEMENTS_ROOT.$class_name.static::PHP_FILE_EXTENSION))
			{
				require_once ELEMENTS_ROOT.$class_name.static::PHP_FILE_EXTENSION;
			}
			else
			{
				if($this->throwOnFailure === true)
				{
					throw new Exception("Class Not Found: ".$class_name,Error::LOADER_ERROR);
				}
			}
		}

		return true;
	}
	
	/**
	 * Loads a class from the library folder.
	 * @param string $class_name
	 * @param string $alias
	 * @throws Exception
	 * @return boolean
	 */
	protected function loadLibraryClass($class_name, $alias = NULL)
	{
		//Correct for a leading \ character
		if(substr($class_name, 0,1) == '\\') $class_name = substr($class_name, 1);
		
		//Split the class into it's namespace components.
		$namespace = explode('\\',$class_name);
		
		//Set the final class name
		$className = $namespace[count($namespace)-1];
		
		//Path for classes
		$path = LIBRARY_ROOT;
		for($i = 0; $i < count($namespace)-1; $i++)
		{
			$path .= $namespace[$i].DIRECTORY_SEPARATOR;
		}
			
		//Sub namespace switches
		if(array_key_exists(1, $namespace))
		{
			switch($namespace[1])
			{
				case 'Traits':
					$extension = static::TRAIT_FILE_EXTENSION;
					break;
				default:
					$extension = static::CLASS_FILE_EXTENSION;
					break;
			}
		}
		else
		{
			$extension = static::CLASS_FILE_EXTENSION;
		}

		//Location
		$include = $path.$className.$extension;
		if(file_exists($include))
		{
			//Require the class into the project
			require_once $include;
			
			//Alias the newly loaded class
			if(isset($alias))
				Alias::load($alias, false);
			
			//Return true on success
			return true;
		}
		else
		{
			//Throw exception when we can't load the class
			throw new Exception('Error Loading Library Class: '.$class_name, 501);
		}
	}
	
	/**
	 * Load a custom controller into the application
	 * @param string $class_name
	 * @throws Exception
	 */
	protected function loadController($class_name)
	{
		$include = CONTROLLER_ROOT.$class_name.static::PHP_FILE_EXTENSION;
		if(file_exists($include))
		{
			require_once $include;
		}
		else
		{
			if($this->throwOnFailure === true)
			{
				throw new PageNotFoundException('Page Not Found',Error::PAGE_NOT_FOUND);
			}
		}

		return true;
	}
	
	/**
	 * Load a custom model in the application
	 * @param string $class_name
	 * @throws Exception
	 */
	protected function loadModel($class_name)
	{
		$include = MODEL_ROOT.$class_name.static::PHP_FILE_EXTENSION;
		if(file_exists($include))
		{
			require_once $include;
		}
		else
		{
			if($this->throwOnFailure === true)
			{
				throw new Exception('Model Not Found',Error::LOADER_ERROR);
			}
		}

		return true;
	}
	
	/**
	 * Load a custom form into the application
	 * @param string $class_name
	 * @throws Exception
	 */
	protected function loadForm($class_name)
	{
		$include = FORMS_ROOT.$class_name.static::PHP_FILE_EXTENSION;
		if(file_exists($include))
		{
			require_once $include;
		}
		else
		{
			if($this->throwOnFailure === true)
			{
				throw new Exception('Form Not Found',Error::LOADER_ERROR);
			}
		}

		return true;
	}
	
	/**
	 * Load a View into the application
	 * @param string $controller
	 * @param string $view
	 * @param bool $required
	 */
	public function loadView($controller,$view,$required = false)
	{
		foreach($this->viewSearchDirectories as $dir)
		{
			$theView = $dir;
			if(substr($theView,strlen($theView)-2) == DIRECTORY_SEPARATOR)
			{
				$theView .= DIRECTORY_SEPARATOR;
			}
			$theView .= $controller.DIRECTORY_SEPARATOR.$view.static::VIEW_FILE_EXTENSION;
			if(file_exists($theView))
			{
				return $theView;
			}
		}
		if($required === true)
		{
			throw new Exception('Failed to load the view.', Error::LOADER_ERROR);
		}

		return true;
	}
	
	/**
	 * Load a View into the application
	 * @param string $controller
	 * @param string $view
	 * @param bool $required
	 */
	public function loadLayout($name)
	{
		foreach($this->layoutSearchDirectories as $dir)
		{
			$theLayout = $dir;
			if(substr($theLayout,strlen($theLayout)-2) == DIRECTORY_SEPARATOR)
			{
				$theLayout .= DIRECTORY_SEPARATOR;
			}
			$theLayout .= $name.static::VIEW_FILE_EXTENSION;
			if(file_exists($theLayout))
			{
				return $theLayout;
			}
		}
		throw new Exception('Unable to locate layout.', Error::LOADER_ERROR);
	}
	
	/**
	 * Return the value of $throwOnFailure
	 * @return bool $throwOnFailure
	 */
	public function getThrowOnFailure()
	{
		return $this->throwOnFailure;
	}

	/**
	 * Allows the programmer to disable thrown exceptions when failing to load classes. Allows another loading system to take over and load the class.
	 * @param boolean $throwOnFailure
	 */
	public function setThrowOnFailure($throwOnFailure)
	{
		$this->throwOnFailure = (bool)$throwOnFailure;
		return $this;
	}
	/**
	 * @return string $controllerSuffix
	 */
	public function getControllerSuffix()
	{
		return $this->controllerSuffix;
	}

	/**
	 * @return string $formSuffix
	 */
	public function getFormSuffix()
	{
		return $this->formSuffix;
	}

	/**
	 * @return string $modelSuffix
	 */
	public function getModelSuffix()
	{
		return $this->modelSuffix;
	}

	/**
	 * @param string $controllerSuffix
	 */
	private function setControllerSuffix($controllerSuffix)
	{
		$this->controllerSuffix = $controllerSuffix;
		return $this;
	}

	/**
	 * @param string $formSuffix
	 */
	private function setFormSuffix($formSuffix)
	{
		$this->formSuffix = $formSuffix;
		return $this;
	}

	/**
	 * @param string $modelSuffix
	 */
	private function setModelSuffix($modelSuffix)
	{
		$this->modelSuffix = $modelSuffix;
		return $this;
	}
	
	
	/**
	 * Add a search directory for the application to look for controller class files. The second parameter will make the new directory take precedence
	 * over any previous directories. It is the default to add new directories as the primary directory.
	 * @param string $dir
	 */
	public function addControllerSearchDirectory($dir, $primary = true)
	{
		if($primary === true)
		{
			array_unshift($this->controllerSearchDirectories, $dir);
		}
		else
		{
			array_push($this->controllerSearchDirectories, $dir);
		}
		return $this;
	}
	
	/**
	 * Add a search directory for the application to look for form class files. The second parameter will make the new directory take precedence
	 * over any previous directories. It is the default to add new directories as the primary directory.
	 * @param string $dir
	 */
	public function addFormSearchDirectory($dir, $primary = true)
	{
		if($primary === true)
		{
			array_unshift($this->formSearchDirectories, $dir);
		}
		else
		{
			array_push($this->formSearchDirectories, $dir);
		}
		return $this;
	}
	
	/**
	 * Add a search directory for the application to look for model class files. The second parameter will make the new directory take precedence
	 * over any previous directories. It is the default to add new directories as the primary directory.
	 * @param string $dir
	 */
	public function addModelSearchDirectory($dir, $primary = true)
	{
		if($primary === true)
		{
			array_unshift($this->modelSearchDirectories, $dir);
		}
		else
		{
			array_push($this->modelSearchDirectories, $dir);
		}
		return $this;
	}
	
	/**
	 * Add a search directory for the application to look for view files. The second parameter will make the new directory take precedence
	 * over any previous directories. It is the default to add new directories as the primary directory.
	 * @param string $dir
	 */
	public function addLayoutSearchDirectory($dir, $primary = true)
	{
		if($primary === true)
		{
			array_unshift($this->layoutSearchDirectories, $dir);
		}
		else
		{
			array_push($this->layoutSearchDirectories, $dir);
		}
		return $this;
	}
	
	/**
	 * Add a search directory for the application to look for view files. The second parameter will make the new directory take precedence
	 * over any previous directories. It is the default to add new directories as the primary directory.
	 * @param string $dir
	 */
	public function addViewSearchDirectory($dir, $primary = true)
	{
		if($primary === true)
		{
			array_unshift($this->viewSearchDirectories, $dir);
		}
		else
		{
			array_push($this->viewSearchDirectories, $dir);
		}
		return $this;
	}
	/**
	 * @return array[string] $controllerSearchDirectories
	 */
	public function getControllerSearchDirectories()
	{
		return $this->controllerSearchDirectories;
	}

	/**
	 * @return array[string] $formSearchDirectories
	 */
	public function getFormSearchDirectories()
	{
		return $this->formSearchDirectories;
	}

	/**
	 * @return array[string] $modelSearchDirectories
	 */
	public function getModelSearchDirectories()
	{
		return $this->modelSearchDirectories;
	}

	/**
	 * @return array[string] $viewSearchDirectories
	 */
	public function getViewSearchDirectories()
	{
		return $this->viewSearchDirectories;
	}

	/**
	 * @return array[string] $layoutSearchDirectories
	 */
	public function getLayoutSearchDirectories()
	{
		return $this->layoutSearchDirectories;
	}

	/**
	 * @param array[string] $layoutSearchDirectories
	 */
	public function setLayoutSearchDirectories(array $layoutSearchDirectories)
	{
		$this->layoutSearchDirectories = $layoutSearchDirectories;
		return $this;
	}

	/**
	 * @param array[string] $controllerSearchDirectories
	 */
	public function setControllerSearchDirectories(array $controllerSearchDirectories)
	{
		$this->controllerSearchDirectories = $controllerSearchDirectories;
		return $this;
	}

	/**
	 * @param array[string] $formSearchDirectories
	 */
	public function setFormSearchDirectories(array $formSearchDirectories)
	{
		$this->formSearchDirectories = $formSearchDirectories;
		return $this;
	}

	/**
	 * @param array[string] $modelSearchDirectories
	 */
	public function setModelSearchDirectories(array $modelSearchDirectories)
	{
		$this->modelSearchDirectories = $modelSearchDirectories;
		return $this;
	}

	/**
	 * @param array[string] $viewSearchDirectories
	 */
	public function setViewSearchDirectories(array $viewSearchDirectories)
	{
		$this->viewSearchDirectories = $viewSearchDirectories;
		return $this;
	}
}