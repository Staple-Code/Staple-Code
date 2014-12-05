<?php
namespace Staple;

class Alias
{
	public static function generate()
	{
		class_alias('\\Staple\\Controller','\\Controller');
		class_alias('\\Staple\\Form\\Form','Form');
		class_alias('\\Staple\\Dev','Dev');
	}
}

?>