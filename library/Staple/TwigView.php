<?php
/**
 * 
 * The TwigView object. This object renders the view, if found. It also provides functionality for
 * stripping tags and html escaping. It holds a dynamic data store for controllers to add items
 * to the view on the fly.
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

use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigView extends View
{
	use Traits\Helpers;

	/**
	 * This function renders the view. If accepts a string representing the controller and
	 * a string representing the requested action. With this information the correct view
	 * is selected and rendered.
	 * @return string
	 */
	public function build()
	{
		if($this->_render === true)
		{
			if (isset($this->_staticView))
			{
				//Load the view from the static view content folder - Possibly move this to the auto loader at a later date.
				$view = STATIC_ROOT . $this->_staticView . '.' . static::STATIC_VIEW_EXTENSION;
				if (file_exists($view))
				{
					//include the view
					include $view;
				}
			}
			else
			{
				//Load the view from the default loader
				$controller = isset($this->_controller) ? $this->_controller : Main::get()->getRoute()->getController();
				$view = Main::get()->getLoader()->loadView($controller, $this->getView());
				if (strlen($view) >= 1 && $view !== false)
				{
					//Initialize the view model, if set
					if (isset($this->_viewModel))
						$this->addData('model',$this->_viewModel);

					//Setup local $form variable, if set
					if(isset($this->_viewForm))
						$this->addData('form',$this->_viewForm);

					$viewContents = file_get_contents($view);

					//Make a Twig
					$twigLoader = new ArrayLoader(['view.html'=>$viewContents]);
					$twig = new Environment($twigLoader);

					//include the view
					$twig->display('view.html',(array)$this->_store);
				}
			}
		}
		else
		{
			//skip rendering of an additional views
			$this->_render = false;
		}
	}
}