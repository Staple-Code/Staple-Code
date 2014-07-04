<?php
/** 
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
		$download = false;
		$version = NULL;
		switch($_GET['v'])
		{
			case '0.5.1':
			case '0.5.9':
			case '0.7.6':
				$download = true;
				$version = $_GET['v'];
				break;
		}
		if($download === TRUE && $version != NULL)
		{
			$this->layout->setName('blank');
			
			$mail = new Staple_Mail();
			$mail->addTo('contact@ironpilot.net')
				->addTo('ironpilot@gmail.com')
				->setSubject('Staple Download')
				->setBody("Staple was just downloaded. <br><br>At:".date('m-d-Y H:i:s')."<br>Version: $version <br>User Agent:".$_SERVER["HTTP_USER_AGENT"])
				->Send();
				
			header('Content-type: application/zip');
			header('Content-Disposition: attachment; filename="staple-'.$version.'.zip"');
			ob_clean();
			readfile(FOLDER_ROOT.'/htdocs/downloads/staple-'.$version.'.zip');
			Staple_Main::get()->excludeHeaderFooter();
			$this->view->noRender();
		}
	}
}

?>