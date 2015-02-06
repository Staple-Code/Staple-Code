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
class Staple_Autoload
{
	/**
	 * 
	 * Automatically loads class files for the application.
	 * @param string $class_name
	 * @throws Exception
	 */
	public static function load($class_name)
	{
		if(substr($class_name,0,7) == 'Staple_')
		{
			$class_name = str_replace('Staple_','',$class_name);
			$include = LIBRARY_ROOT.'Staple/'.$class_name.'.class.php';
			if(file_exists($include))
			{ 
				include $include;
			}
			else
			{
				throw new Exception('Error Loading Framework', Staple_Error::LOADER_ERROR);
			}
		}
		elseif(substr($class_name,strlen($class_name)-10,10) == "Controller")
		{
			$include = PROGRAM_ROOT.'controllers/'.$class_name.'.php';
			if(file_exists($include))
			{ 
				include $include;
			}
			else
			{
				throw new Exception('Page Not Found',Staple_Error::LOADER_ERROR);
			}
		}
		elseif(substr($class_name,strlen($class_name)-5,5) == "Model")
		{
			$include = PROGRAM_ROOT.'models/'.$class_name.'.php';
			if(file_exists($include))
			{ 
				include $include;
			}
			else
			{
				throw new Exception('Model Not Found',Staple_Error::LOADER_ERROR);
			}
		}
		else
		{
			if(file_exists(ELEMENTS_ROOT.$class_name.'.php'))
			{
				include_once ELEMENTS_ROOT.$class_name.'.php';
			}
			else
			{
				throw new Exception("Error Loading Site",Staple_Error::LOADER_ERROR);
			}
		}
	}
}