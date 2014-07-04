<?php
class Staple_Form_Filter_ToDateTime extends Staple_Form_Filter
{
	/* (non-PHPdoc)
	 * @see Staple_Form_Filter::filter()
	 */
	public function filter($text)
	{
		try {
			return new DateTime($text);
		}
		catch (Exception $e)
		{
			return new DateTime();
		}
	}

	/* (non-PHPdoc)
	 * @see Staple_Form_Filter::getName()
	 */
	public function getName()
	{
		return 'datetime';
	}


}

?>