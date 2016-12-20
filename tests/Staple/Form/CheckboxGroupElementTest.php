<?php
/**
 * Created by PhpStorm.
 * User: scott.henscheid
 * Date: 12/19/2016
 * Time: 1:19 PM
 */

namespace Staple\Tests;

use PHPUnit\Framework\TestCase;
use Staple\Form\CheckboxElement;
use Staple\Form\CheckboxGroupElement;
use Staple\Form\ViewAdapters\BootstrapViewAdapter;
use Staple\Form\ViewAdapters\FoundationViewAdapter;
use \Exception;

class CheckboxGroupElementTest extends TestCase
{
	const STANDARD_BUILD = "<div class=\"form_element element_checkboxgroup\" id=\"TestCheckboxGroupElement_element\">\n\t<label class=\"form_element element_checkboxgroup\">My Test Checkbox Group Element</label>\n\t<div class=\"form_checkboxes\">\n\t\t<div class=\"form_element element_checkbox\" id=\"Checkbox1_element\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox1\" name=\"Checkbox1\" value=\"1\" class=\"form_element element_checkbox\">\n\t\t\t<label for=\"Checkbox1\" class=\"form_element element_checkbox\">My First Checkbox</label>\n\t\t</div>\n\t\t<div class=\"form_element element_checkbox\" id=\"Checkbox2_element\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox2\" name=\"Checkbox2\" value=\"1\" class=\"form_element element_checkbox\">\n\t\t\t<label for=\"Checkbox2\" class=\"form_element element_checkbox\">My Second Checkbox</label>\n\t\t</div>\n\t\t<div class=\"form_element element_checkbox\" id=\"Checkbox3_element\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox3\" name=\"Checkbox3\" value=\"1\" class=\"form_element element_checkbox\">\n\t\t\t<label for=\"Checkbox3\" class=\"form_element element_checkbox\">My Third Checkbox</label>\n\t\t</div>\n\t</div>\n</div>\n";
	const FOUNDATION_BUILD = "<div  class=\"row\">\n\t<div class=\"small-12 columns\">\n\t\t<label class=\"row\">My Test Checkbox Group Element</label>\n\t</div>\n\t<div class=\"small-12 columns\">\n\t<div class=\"form_checkboxes\">\n\t<div class=\"row\">\n\t\t<div class=\"small-12 columns\">\n\t\t\t<label for=\"Checkbox1\" class=\"row\">My First Checkbox</label>\n\t\t</div>\n\t\t<div class=\"small-12 columns\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox1\" name=\"Checkbox1\" value=\"1\" class=\"row\">\n\t\t</div>\n\t</div>\n\t<div class=\"row\">\n\t\t<div class=\"small-12 columns\">\n\t\t\t<label for=\"Checkbox2\" class=\"row\">My Second Checkbox</label>\n\t\t</div>\n\t\t<div class=\"small-12 columns\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox2\" name=\"Checkbox2\" value=\"1\" class=\"row\">\n\t\t</div>\n\t</div>\n\t<div class=\"row\">\n\t\t<div class=\"small-12 columns\">\n\t\t\t<label for=\"Checkbox3\" class=\"row\">My Third Checkbox</label>\n\t\t</div>\n\t\t<div class=\"small-12 columns\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox3\" name=\"Checkbox3\" value=\"1\" class=\"row\">\n\t\t</div>\n\t</div>\n\t</div>\n\t</div>\n</div>\n";
	const BOOTSTRAP_BUILD = "<div class=\"form-group\">\n\t<label class=\"control-label\">My Test Checkbox Group Element</label>\n\t<div class=\"form_checkboxes\">\n\t\t<div class=\"checkbox\">\n\t\t\t<label class=\"control-label\" for=\"Checkbox1\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox1\" name=\"Checkbox1\" value=\"1\">\n\t\t\tMy First Checkbox</label>\n\t\t</div>\n\t\t\n\t\t<div class=\"checkbox\">\n\t\t\t<label class=\"control-label\" for=\"Checkbox2\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox2\" name=\"Checkbox2\" value=\"1\">\n\t\t\tMy Second Checkbox</label>\n\t\t</div>\n\t\t\n\t\t<div class=\"checkbox\">\n\t\t\t<label class=\"control-label\" for=\"Checkbox3\">\n\t\t\t<input type=\"checkbox\" id=\"Checkbox3\" name=\"Checkbox3\" value=\"1\">\n\t\t\tMy Third Checkbox</label>\n\t\t</div>\n\t\t\n\t</div>\n</div>\n";
	/**
	 * @return CheckboxGroupElement
	 */
	private function getTestCheckboxElement()
	{
		return CheckboxGroupElement::create('TestCheckboxGroupElement','My Test Checkbox Group Element')
			->addCheckboxArray([
				CheckboxElement::create('Checkbox1','My First Checkbox'),
				CheckboxElement::create('Checkbox2','My Second Checkbox'),
				CheckboxElement::create('Checkbox3','My Third Checkbox')
			]);
	}

	private function getFoundationViewAdapter()
	{
		return new FoundationViewAdapter();
	}

	private function getBootstrapViewAdapter()
	{
		return new BootstrapViewAdapter();
	}

	/**
	 * Standard Output Build Test
	 * @test
	 */
	public function testStandardBuild()
	{
		$element = $this->getTestCheckboxElement();

		$buf = $element->build();

		$this->assertEquals(self::STANDARD_BUILD,$buf);
	}

	/**
	 * Test Foundation Build for this field.
	 * @test
	 */
	public function testFoundationBuild()
	{
		$element = $this->getTestCheckboxElement();
		$element->setElementViewAdapter($this->getFoundationViewAdapter());

		$buf = $element->build();

		$this->assertEquals(self::FOUNDATION_BUILD,$buf);
	}

	/**
	 * Test Bootstrap Build for this field
	 * @test
	 */
	public function testBootstrapBuild()
	{
		$element = $this->getTestCheckboxElement();
		$element->setElementViewAdapter($this->getBootstrapViewAdapter());

		$buf = $element->build();

		$this->assertEquals(self::BOOTSTRAP_BUILD,$buf);
	}

	/**
	 * Test that we can set and retrieve values from the object
	 * @test
	 * @throws \Exception
	 */
	public function testValueSetAndRetrieve()
	{
		//Setup
		$element = $this->getTestCheckboxElement();
		$boxes = $element->getBoxes();

		$this->assertArrayHasKey('Checkbox1',$element->getValue());
		$this->assertFalse($boxes[0]->isChecked());
		$this->assertEquals(false,$boxes[0]->getValue());
		$this->assertArrayHasKey('Checkbox2',$element->getValue());
		$this->assertFalse($boxes[1]->isChecked());
		$this->assertEquals(false,$boxes[1]->getValue());
		$this->assertArrayHasKey('Checkbox3',$element->getValue());
		$this->assertFalse($boxes[2]->isChecked());
		$this->assertEquals(false,$boxes[2]->getValue());

		//Act
		$element->setValue([
			'Checkbox1'=>'one',
			'Checkbox2'=>'two',
			'Checkbox3'=>'three',
		]);

		$this->assertArrayHasKey('Checkbox1',$element->getValue());
		$this->assertTrue($boxes[0]->isChecked());
		$this->assertEquals(true,$boxes[0]->getValue());
		$this->assertArrayHasKey('Checkbox2',$element->getValue());
		$this->assertTrue($boxes[1]->isChecked());
		$this->assertEquals(true,$boxes[1]->getValue());
		$this->assertArrayHasKey('Checkbox3',$element->getValue());
		$this->assertTrue($boxes[2]->isChecked());
		$this->assertEquals(true,$boxes[2]->getValue());

		$this->expectException(Exception::class);

		//This should throw an exception
		$element->setValue(true);
	}
}
