<?php
/**
 * The autoloader class helps to load controllers and models as well as user objects
 * when they are requested by the application.
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
namespace Staple;

use \Exception;

class Autoload
{
    const STAPLE_NAMESPACE = 'Staple';
	const CONTROLLER_SUFFIX = 'Controller';
	const FORM_SUFFIX = 'Form';
	const MODEL_SUFFIX = 'Model';
	const STAPLE_PREFIX = 'Staple_';
	const STAPLE_TRAIT_PREFIX = 'Staple_Trait';
	const PHP_FILE_EXTENSION = '.php';
	const CLASS_FILE_EXTENSION = '.class.php';
	const TRAIT_FILE_EXTENSION = '.trait.php';
	const VIEW_FILE_EXTENSION = '.phtml';
	const TRAIT_FOLDER = 'Traits';
	
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
	 * Function alias for the loadClass() method
	 * @param string $class_name
	 */
	public function load($class_name)
	{
		return $this->loadClass($class_name);
	}
	
	/**
	 * Load a class into the application
	 * @param string $class_name
	 * @throws Exception
	 */
	public function loadClass($class_name)
	{
	    //Split the class into it's namespace components.
	    $namespace = explode('\\',$class_name);
	    
	    //Set the final class name
	    $className = $namespace[count($namespace)-1];
	    
	    if($namespace[0] == static::STAPLE_NAMESPACE)       //Look for STAPLE Namespace
	    {
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
            
            //Location
            $include = $path.$className.$extension;
            if(file_exists($include))
            {
                require_once $include;
            }
            else
            {
                throw new Exception('Error Loading Framework: '.$class_name, 501);
            }
	    }
		elseif(substr($class_name,strlen($class_name)-strlen($this->getControllerSuffix()),strlen($this->getControllerSuffix())) == $this->getControllerSuffix())			//Look for Controllers
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
					throw new Exception('Page Not Found',Error::PAGE_NOT_FOUND);
				}
			}
		}
		elseif(substr($class_name,strlen($class_name)-strlen($this->getModelSuffix()),strlen($this->getModelSuffix())) == $this->getModelSuffix())					//Look for Models
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
		}
		elseif(substr($class_name,strlen($class_name)-4,4) == "Form")					//Look for Forms
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
		}
		/*elseif(substr($class_name,0,5) == 'Zend_' && file_exists(LIBRARY_ROOT.'Zend/Loader.php'))		//Look for Zend Classes
		{
			//Add Library Root to Include Path
			if(strpos(get_include_path(), LIBRARY_ROOT) === false)
			{
				set_include_path(get_include_path().PATH_SEPARATOR.LIBRARY_ROOT);
			}
			try{
				require_once LIBRARY_ROOT . 'Zend/Loader.php';
				if(class_exists('Zend_Loader'))
				{
					Zend_Loader::loadClass($class_name);
				}
			}
			catch(Exception $e)
			{
				if($this->throwOnFailure === true)
				{
					throw new Exception('Zend Loader Not Found',Staple_Error::LOADER_ERROR);
				}
			}
		}*/
		else																							//Look for other elements
		{
			if(file_exists(ELEMENTS_ROOT.$class_name.static::PHP_FILE_EXTENSION))
			{
				require_once ELEMENTS_ROOT.$class_name.static::PHP_FILE_EXTENSION;
			}
			else
			{
				if($this->throwOnFailure === true)
				{
					throw new Exception("Class Not Found".$class_name,Error::LOADER_ERROR);
				}
			}
		}
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
	 * @return the $throwOnFailure
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
	 * @return the $controllerSuffix
	 */
	public function getControllerSuffix()
	{
		return $this->controllerSuffix;
	}

	/**
	 * @return the $formSuffix
	 */
	public function getFormSuffix()
	{
		return $this->formSuffix;
	}

	/**
	 * @return the $modelSuffix
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
	 * @return the $controllerSearchDirectories
	 */
	public function getControllerSearchDirectories()
	{
		return $this->controllerSearchDirectories;
	}

	/**
	 * @return the $formSearchDirectories
	 */
	public function getFormSearchDirectories()
	{
		return $this->formSearchDirectories;
	}

	/**
	 * @return the $modelSearchDirectories
	 */
	public function getModelSearchDirectories()
	{
		return $this->modelSearchDirectories;
	}

	/**
	 * @return the $viewSearchDirectories
	 */
	public function getViewSearchDirectories()
	{
		return $this->viewSearchDirectories;
	}

	/**
	 * @return the $layoutSearchDirectories
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