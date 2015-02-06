<?php

/** 
 * Image Manipluation class. This class basically encapulates PHP's built in GD2
 * functions and makes them more accessible.
 * 
 * Currently supported image types: JPEG, GIF, PNG
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
 * 
 */
class Staple_Image
{
	const MIME_JPG = 'image/jpeg';
	const MIME_GIF = 'image/gif';
	const MIME_PNG = 'image/png';
	
	/**
	 * Array of MIME Types supported by this API with keys for their file extensions.
	 * @var array
	 */
	protected static $mimes = array(
						'jpg'=>MIME_JPG,
						'gif'=>MIME_GIF,
						'png'=>MIME_PNG
						);

	/**
	 * Working Image Resource.
	 * @var resource
	 */
	protected $image;
	
	/**
	 * A string symbolizing the source image file.
	 * @var string
	 */
	protected $source;
	
	/**
	 * 
	 * A string that denotes the destination location.
	 * @var string
	 */
	protected $destination;
	
	/**
	 * Height of the image in pixels.
	 * @var int
	 */
	protected $height;
	
	/**
	 * Width of the image in pixels.
	 * @var int
	 */
	protected $width;
	
	/**
	 * String that holds the MIME type of the current image.
	 * @var string
	 */
	protected $mime;
	
	/**
	 * Quality is a number between 1 and 100 determining the quality of the JPEG image
	 * @var int
	 */
	protected $quality = 100;
	
	/**
	 * Preserve the aspect ratio of the image.
	 * @var bool
	 */
	protected $preserve = true;

	/**
	 * The constructor accepts a source file location to base the new image off of.
	 * @param string $source
	 * @throws Exception
	 */
	public function __construct($source = NULL)
	{
		if(isset($source))
		{
			if(file_exists($source))
			{
				$this->setImageSettings();
				$this->createImage();
			}
			else
			{
				throw new Exception('Image File Not Found', Staple_Error::APPLICATION_ERROR);
			}
		}
	}
	
	/**
	 * Returns an array of the supported MIME types for this extension.
	 * @return array
	 */
	public static function getMimeTypes()
	{
		return self::$mimes;
	}
	
	/**
	 * Encapsulates creation of the object using the factory pattern.
	 * @param string $source
	 * @throws Exception
	 * @return Staple_Image
	 */
	public static function Create($source = NULL)
	{
		try 
		{
			return new self($source);
		}
		catch (Exception $e)
		{
			throw new Exception('Error creating image object', Staple_Error::APPLICATION_ERROR);
		}
	}
	
	/**
	 * Creates the image resource from the class source.
	 * @throws Exception
	 */
	protected function createImage()
	{
		$img = false;
		switch($this->mime)
		{
			case self::MIME_JPG :
				$img = imagecreatefromjpeg($this->source);
				break;
			case self::MIME_GIF :
				$img = imagecreatefromgif($this->source);
				break;
			case self::MIME_PNG :
				$img = imagecreatefrompng($this->source);
				break;
		}
		if($img !== false)
		{
			$this->image = $img;
		}
		else
		{
			throw new Exception('Invalid Image Type',Staple_Error::APPLICATION_ERROR);
		}
	}
	
	public function Resize(array $dims)
	{
		$dims = array_values($dims);
		
		if($this->width >= $this->height)
		{
			$newwidth = $dims[0];
			$newheight = round($this->height/($this->width/$dims[0]));
		}
		else
		{
			$newheight = $dims[1];
			$newwidth = round($this->width/($this->height/$dims[1]));
		}
		
		if(!isset($this->image))
		{
			$this->image = $this->createImage();
		}
		$newimage = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled ($newimage, $this->image, 0, 0, 0, 0, $newwidth, $newheight, $this->width, $this->height);
		if($this->settings['image']['watermark']['enable'] === true)
		{
			if($this->settings['image']['watermark'][$size] === true)
			{
				list($water_width, $water_height) = getImageSize($this->fileroot.$this->settings['image']['watermarkFile']);
				$watermark = imagecreatefrompng($this->fileroot.$this->settings['image']['watermarkFile']);
				imagecopyresampled ($newimage, $watermark, 0, 0, 0, 0, $newwidth, $newheight, $water_width, $water_height);
			}
		}
		imagedestroy($this->image);
		
		
		//echo "File Created: ".dirname($filename)."/".$size."/".basename($filename);
	}
	
	public function Save($dest = NULL, $type = NULL)
	{
		if(isset($dest))
		{
			$this->destination = $dest;
		}
		if(isset($this->destination))
		{
			throw new Exception('No destination.', Staple_Error::APPLICATION_ERROR);
		}
		else
		{
			if(!file_exists(dirname($this->destination)))
			{
				self::createDir(dirname($this->destination));
			}
			imagejpeg($this->image, $this->destination, $this->quality);
		}
	}
	
	protected static function createDir($dir)
	{
		if(!file_exists(dirname($dir)))
		{
			self::createDir($dir);
		}
		mkdir($dir);
	}
	
	protected function setImageSettings()
	{
		if(($size = getimagesize($source)) !== false)
		{
			$this->source = $source;
			$this->width = $size[0];
			$this->height = $size[1];
			if(array_search($size['mime'], self::$mimes) === FALSE)
			{
				throw new Exception('Unsupported MIME Type', Staple_Error::APPLICATION_ERROR);
			}
			else
			{
				$this->mime = $size['mime'];
			}
		}
		else
		{
			throw new Exception('Unable to get image properties', Staple_Error::APPLICATION_ERROR);
		}
	}
	
	/**
	 * Sets a new source image to manipulate. This could be useful if an image source
	 * was not specified on construction.
	 * @param string $source
	 */
	public function setSource($source)
	{
		$this->source = $source;
		$this->setImageSettings();
		$this->createImage();
		return $this;
	}
}

?>