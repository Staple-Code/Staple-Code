<?php
/** 
 * @author Ironpilot
 * 
 * 
 */
class Staple_Form_Filter_Trim extends Staple_Form_Filter
{

	/**
	 * 
	 * @see Staple_Form_Filter::filter()
	 */
	public function filter($text)
	{
		return trim($text);
	}
	/**
	 * 
	 * @see Staple_Form_Filter::getName()
	 */
	public function getName()
	{
		return 'trim';
	}

}

?>