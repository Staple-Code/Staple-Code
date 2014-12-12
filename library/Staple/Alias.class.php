<?php
namespace Staple;

class Alias
{
	public static function generate()
	{
		class_alias('\\Staple\\Main','Main');
		class_alias('\\Staple\\Autoload','Autoload');
		class_alias('\\Staple\\Alias','Alias');
	}
}

?>