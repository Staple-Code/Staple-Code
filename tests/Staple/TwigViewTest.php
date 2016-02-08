<?php
/**
 * Created by PhpStorm.
 * User: ironpilot
 * Date: 10/28/15
 * Time: 6:57 PM
 */

namespace Staple\Tests;


use Staple\TwigView;

class TwigViewTest extends \PHPUnit_Framework_TestCase
{
	const TWIG_RENDER_SIMPLE = "<h2>Twig Render Test</h2>\n\nTest Variable Value";
	/**
	 * @return TwigView
	 */
	private function getTestObject()
	{
		return TwigView::create()
			->setView('testTwig')
			->setController('index');
	}

	public function testTwigViewBuild()
	{
		$view = $this->getTestObject();
		$view->vartest = 'Test Variable Value';

		ob_start();
		$view->build();
		$render = ob_get_contents();
		ob_end_clean();

		$this->assertEquals(self::TWIG_RENDER_SIMPLE,$render);
	}
}
