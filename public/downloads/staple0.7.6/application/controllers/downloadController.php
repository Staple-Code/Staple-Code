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
class downloadController extends Staple_Controller
{
	/**
	 * 
	 * @see Staple_Controller::index()
	 */
	public function _start()
	{
		$this->_openAll();
	}
	public function index()
	{
		
	}
	public function download()
	{
		$mail = new Staple_Mail();
		$mail->addTo('contact@ironpilot.net')
			->addTo('ironpilot@gmail.com')
			->setSubject('Staple Download')
			->setBody('Staple was just downloaded: '.date('d-m-Y H:i:s'))
			->Send();
			
		header('Content-type: application/zip');
		header('Content-Disposition: attachment; filename="staple-0.5.1.zip"');
		ob_clean();
		readfile(FOLDER_ROOT.'/htdocs/downloads/staple-0.5.1.zip');
		Staple_Main::get()->excludeHeaderFooter();
		$this->view->noRender();
	}
}

?>