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
namespace Staple;

use \Staple\Error;
use \Exception;

class Image
{
	const MIME_JPG = 'image/jpeg';
	const MIME_GIF = 'image/gif';
	const MIME_PNG = 'image/png';
	
	/**
	 * Array of MIME Types supported by this API with keys for their file extensions.
	 * @var array
	 */
	protected static $mimes = array(
						'jpg'=>self::MIME_JPG,
						'gif'=>self::MIME_GIF,
						'png'=>self::MIME_PNG
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
				$this->setSource($source);
				$this->setImageSettings();
				$this->createImage();
			}
			else
			{
				throw new Exception('Image File Not Found', Error::APPLICATION_ERROR);
			}
		}
	}
	

	/**
	 * Encapsulates creation of the object using the factory pattern.
	 * @param string $source
	 * @throws Exception
	 * @return Image
	 */
	public static function Create($source = NULL)
	{
		try 
		{
			return new self($source);
		}
		catch (Exception $e)
		{
			throw new Exception('Error creating image object', Error::APPLICATION_ERROR);
		}
	}
	
	/**
	 * Creates an image object from an upload key name.
	 * @param string $uploadKeyName
	 * @return Image
	 */
	public static function CreateFromUpload($uploadKeyName)
	{
		if(isset($_FILES[$uploadKeyName]))
			return self::Create($_FILES[$uploadKeyName]['tmp_name']);
		else
			return NULL;
	}

	/**
	 * Get the image resource
	 * @return resource resource
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * Set the image resource
	 * @param resource $image
	 * @return $this
	 */
	protected function setImage($image)
	{
		//Destroy any previous image to save memory.
		if(isset($this->image))
		{
			imagedestroy($this->image);
		}
		
		//Check that the supplied variable contains a resource.
		if(is_resource($image))
		{
			$this->image = $image;
		}
		
		//Return $this
		return $this;
	}

	/**
	 * Get the source string
	 * @return string
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * Get the image destination string
	 * @return string $destination
	 */
	public function getDestination()
	{
		return $this->destination;
	}

	/**
	 * Set the destination string
	 * @param string $destination
	 * @return $this
	 */
	public function setDestination($destination)
	{
		$this->destination = (string)$destination;
		return $this;
	}

	/**
	 * Get the image height
	 * @return int $height
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Set the image height
	 * @param int $height
	 * @return $this
	 */
	public function setHeight($height)
	{
		$this->height = (int)$height;
		return $this;
	}

	/**
	 * Get the image width
	 * @return int $width
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Set the width parameter
	 * @param int $width
	 * @return $this
	 */
	public function setWidth($width)
	{
		$this->width = (int)$width;
		return $this;
	}

	/**
	 * Get the MIME String
	 * @return string $mime
	 */
	public function getMime()
	{
		return $this->mime;
	}

	/**
	 * Get the quality parameter
	 * @return int
	 */
	public function getQuality()
	{
		return $this->quality;
	}

	/**
	 * Set the quality parameter
	 * @param int $quality
	 * @return $this
	 */
	public function setQuality($quality)
	{
		$this->quality = (int)$quality;
		return $this;
	}

	/**
	 *
	 * @return bool
	 */
	public function getPreserve()
	{
		return $this->preserve;
	}

	/**
	 * Set the preserve aspect ratio bool
	 * @param bool $preserve
	 * @return $this
	 */
	public function setPreserve($preserve)
	{
		$this->preserve = (bool)$preserve;
		return $this;
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
	 * Return the extension that matches the MIME type of the current file.
	 * @throws Exception
	 * @return string
	 */
	public function getImageExtension()
	{
		switch($this->mime)
		{
			case self::MIME_JPG :
				$ext = 'jpg';
				break;
			case self::MIME_GIF :
				$ext = 'gif';
				break;
			case self::MIME_PNG :
				$ext = 'png';
				break;
			default:
				throw new Exception('Invalid image type.',Error::APPLICATION_ERROR);
		}
		return $ext;
	}
	
	/**
	 * Creates the image resource from the class source.
	 * @throws Exception
	 */
	protected function createImage()
	{
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
			default:
				throw new Exception('Invalid image type.',Error::APPLICATION_ERROR);
		}
		if($img !== false)
		{
			$this->setImage($img);
		}
		else
		{
			throw new Exception('An error occurred while creating the image resource.',Error::APPLICATION_ERROR);
		}
	}

	/**
	 * Sets preserve aspect to true and resizes the image to fit within the box specified.
	 * @param int $width
	 * @param int $height
	 * @return bool
	 * @throws Exception
	 */
	public function fitInBox($width, $height)
	{
		$this->setPreserve(true);
		return $this->resize($width,$height);
	}
	
	/**
	 * Resize an image to the specified width and height in pixels.
	 * @param int $width
	 * @param int $height
	 * @throws Exception
	 * @return boolean
	 */
	public function resize($width, $height)
	{
		$width = (int)$width;
		$height = (int)$height;
		
		if($this->width >= $this->height)
		{
			$height = round($this->height / ($this->width / $width));
		}
		else
		{
			$width = round($this->width / ($this->height / $height));
		}
		
		if(!isset($this->image))
		{
			$this->createImage();
		}
		$newimage = imagecreatetruecolor($width, $height);
		$success = imagecopyresampled($newimage, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		
		if($success === true)
		{
			$this->setHeight($height);
			$this->setWidth($width);
			$this->setImage($newimage);
			return true;
		}
		else
		{
			throw new Exception('Failed to resize the image. An error occurred while resizing.', Error::APPLICATION_ERROR);
		}
	}
	
	/**
	 * @todo incomplete function
	 */
	public function addWatermark()
	{
		if($this->settings['image']['watermark']['enable'] === true)
		{
			if($this->settings['image']['watermark'][$size] === true)
			{
				list($water_width, $water_height) = getImageSize($this->fileroot.$this->settings['image']['watermarkFile']);
				$watermark = imagecreatefrompng($this->fileroot.$this->settings['image']['watermarkFile']);
				imagecopyresampled ($newimage, $watermark, 0, 0, 0, 0, $width, $height, $water_width, $water_height);
			}
		}
	}
	
	/**
	 * This function will convert the current image to the specified type.
	 * @todo complete this function
	 * @param string $mime
	 * @throws Exception
	 */
	public function convertToType($mime)
	{
		switch($mime)
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
			default:
					throw new Exception('Invalid image type.',Error::APPLICATION_ERROR);
		}
	}
	
	/**
	 * Save the image resource to a destination.
	 * @param string $dest
	 * @throws Exception
	 * @return boolean
	 */
	public function save($dest = NULL)
	{
		//Set a destination if included in the method call
		if(isset($dest))
		{
			$this->setDestination($dest);
		}
		
		//Check that a destination has been set.
		if(!isset($this->destination))
		{
			throw new Exception('No destination.', Error::APPLICATION_ERROR);
		}
		else
		{
			//Check that the root directory exists, if not create it.
			if(!file_exists(dirname($this->destination)))
			{
				self::createDir(dirname($this->destination));
			}
			
			//Save the image to the destination with the correct type.
			$success = false;
			switch($this->getMime())
			{
				case self::MIME_JPG:
					$success = imagejpeg($this->getImage(), $this->getDestination(), $this->getQuality());
					break;
				case self::MIME_GIF:
					$success = imagegif($this->getImage(), $this->getDestination());
					break;
				case self::MIME_PNG:
					$success = imagepng($this->getImage(), $this->getDestination(), $this->getQuality());
					break;
			}
			if($success === true)
			{
				return true;
			}
			else
			{
				throw new Exception('Failed to save image.', Error::APPLICATION_ERROR);
			}
		}
	}
	
	/**
	 * Create a directory to store an image inside.
	 * @param string $dir
	 * @throws Exception
	 * @return boolean
	 */
	protected static function createDir($dir)
	{
		if(mkdir($dir, NULL, true))
		{
			return true;
		}
		else
		{
			throw new Exception('Unable to create directory: '.$dir);
		}
	}
	
	/**
	 * Set the initial image settings.
	 * @throws Exception
	 */
	protected function setImageSettings()
	{
		if(($size = getimagesize($this->getSource())) !== false)
		{
			$this->width = $size[0];
			$this->height = $size[1];
			if(array_search($size['mime'], self::$mimes) === FALSE)
			{
				throw new Exception('Unsupported MIME Type', Error::APPLICATION_ERROR);
			}
			else
			{
				$this->mime = $size['mime'];
			}
		}
		else
		{
			throw new Exception('Unable to get image properties', Error::APPLICATION_ERROR);
		}
	}
	
	/**
	 * Sets a new source image to manipulate. This could be useful if an image source
	 * was not specified on construction.
	 * @param string $source
	 * @return $this
	 */
	public function setSource($source)
	{
		$this->source = $source;
		$this->setImageSettings();
		$this->createImage();
		return $this;
	}
}
