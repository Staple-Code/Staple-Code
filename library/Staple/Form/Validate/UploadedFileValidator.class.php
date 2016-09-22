<?php
/** 
 * Checks that the data references an uploaded file. 
 * Optional parameter will also check the MIME Type of the uploaded file.
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
namespace Staple\Form\Validate;

use \Staple\Form\FieldValidator;
use \finfo;

class UploadedFileValidator extends FieldValidator
{
	const DEFAULT_ERROR = 'File is not valid.';

	/** @var string|string[] $mimeCheck */
	protected $mimeCheck = NULL;
	
	public function __construct($mimetype = NULL, $usermsg = NULL)
	{
		if(isset($usermsg))
		{
			parent::__construct($usermsg);
		}
		if(isset($mimetype))
		{
			$this->setMimeCheck($mimetype);
		}
	}

	/**
	 * @return string $mimeCheck
	 */
	public function getMimeCheck()
	{
		return $this->mimeCheck;
	}

	/**
	 * @param string $mimeCheck
	 * @return $this
	 */
	public function setMimeCheck($mimeCheck)
	{
		$this->mimeCheck = $mimeCheck;
		return $this;
	}

	/**
	 * Run the validation
	 * @param  mixed $data
	 * @return  bool
	 * @see Staple_Form_Validator::check()
	 */
	public function check($data)
	{
		if(is_array($data))
		{
			if(array_key_exists('tmp_name', $data))
			{
				if($this->mimeCheck == null)
				{
					if(is_uploaded_file($data['tmp_name']))
					{
						return true;
					}
				}
				else
				{
					if(array_key_exists('tmp_name',$data))
					{
						if(is_uploaded_file($data['tmp_name']))
						{
							//Check that FileInfo Extension is enabled
							if (class_exists('finfo'))
							{
								$finfo = new finfo(FILEINFO_MIME_TYPE);
								$mime = $this->getMimeCheck();
								if (is_array($mime))
								{
									if (in_array($finfo->file($data['tmp_name']), $mime))
									{
										return true;
									}
								}
								else
								{
									if ($finfo->file($data['tmp_name']) == $mime)
									{
										return true;
									}
								}
							}
						}
					}
				}
			}
		}
		$this->addError();
		return false;
	}
}