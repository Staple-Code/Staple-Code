<?php
class A
{
	function C()
	{
		return 3;
	}
	function D()
	{
		return $this->C();
	}
	function E()
	{
		return B::F();
	}
}
class B extends A
{
	function C()
	{
		return 5;
	}
	static function F()
	{
		return 7;
	}
}

class testsController extends Controller
{
	/* (non-PHPdoc)
	 * @see Staple_Controller::index()
	 */
	public function index()
	{
		$form = new Form();
		
	}

	public function forms()
	{
		$form = new Staple_Form('testform');
		$form->setAction('/tests/forms')
			->setMethod("GET")
			->addField(Staple_Form_TextElement::Create('fname','First Name')->addValidator(new Staple_Form_Validate_Length(10,5)))
			->addField(new Staple_Form_TextElement('lname','Last Name'))
			
			->addField(Staple_Form_TextareaElement::Create('bio','Your Biography')->setRows(5)->setCols(40))
			->addField(
				Staple_Form_SelectElement::Create('birthyear','Year of Birth')
					->addOptionsArray(array('','1994','1995','1996','1997','1998','1999','2000'),true)
				)
			->addField(Staple_Form_RadioGroup::Create('spouse','I need to add a spouse:')
					->addButtonsArray(array('Yes','No'))
					->setValue(1)
				)
			->addField(new Staple_Form_SubmitElement('send','Send Query'));
		
		if($form->wasSubmitted())
		{
			$form->addData($_GET);
			if($form->validate())
			{
				echo 'Form is valid.';
			}
			else
			{
				echo '<p>The following errors occurred:</p>';
			}
		}
		
		$this->view->form = $form;
	}
	public function formcatch()
	{
		echo 'Caught.';
	}
	
	public function layouts()
	{
		
	}
	public function links()
	{
		echo $this->_link(array('downloadTestTest'))."<br>";
		echo $this->_link(array('links'))."<br>";
		echo $this->_link(array('testLinks','testLinks'))."<br>";
		echo $this->_link(array('links'))."<br>";
	}
	
	public function encrypt()
	{
		$key = 'kASMCL^TRB8A<UQwOcgsHDKhgUs[ZtMe';
		$salt = 'askdfRIUF';
		$pepper = 'orpDjk34';
		$string = 'Blah encrypted string.';
		echo "<p>String to Encrypt: $string</p>";
		$encryped = Staple_Encrypt::AES_encrypt($string, $key, $salt, $pepper);
		echo "<p>Encrypted String: ".htmlentities($encryped)."</p>";
		$decrypted = Staple_Encrypt::AES_decrypt($encryped, $key, $salt, $pepper);
		echo "<p>Decrypted String: ".htmlentities($decrypted)."</p>";
	}
	
	public function query()
	{
		//Show me errors for dev purposes.
		ini_set('display_errors', 1);
		
		echo '<h1>Query Test</h1>';
		
		//Setup the database
		$db = Staple_DB::get();
		$db->setUsername('mvjobs')
			->setPassword('Claymore54')
			->setDb('mvjobs_primary')
			->setHost('localhost')
			->connect();
		
		
		$p = new Staple_Query_Select();
		$p->addColumn('name')
			->setTable('article_categories')
			->whereEqual('id', 'articles.cat', true);
			
		//Create the Query 
		$q = new Staple_Query_Select();
		$q
			->setTable('articles')
			->whereIn('articles.id', array(1,2,3,4,5))
			->orderBy(array('articles.name','summary'))
			->limit(3,1)
			->innerJoin('article_categories','articles.cat=article_categories.id');
		
		echo "<p><h3>Query:</h3> ".$q->build()."</p>";
		
		//Execute the Query
		$result = $db->query($q);
		
		echo '<h3>Results:</h3><table border="1" cellspacing="0" cellpadding="5">';
		$first = true;
		if($result instanceof mysqli_result)
		{
			while($myrow = $result->fetch_assoc())
			{
				if($first)
				{
					echo '<tr>';
					foreach($myrow as $name=>$value)
					{
						echo "<th>$name</th>";
					}
					echo '<tr>';
				}
				echo '<tr>';
				foreach($myrow as $value)
				{
					echo "<td>$value</td>";
				}
				$first = false;
				echo '</tr>';
			}
		}
		echo "</table><h3>Object Dump:</h3><h4>Query:</h4>";
		
		Staple_Dev::Dump($q);
		echo "<h4>Result:</h4>";
		Staple_Dev::Dump($result);
		echo "<h4>Error:</h4>";
		Staple_Dev::Dump($db->error);
	}
	
	public function queryinsert()
	{
		//Show me errors for dev purposes.
		ini_set('display_errors', 1);
		
		echo '<h1>Query Test</h1>';
		
		//Setup the database
		$db = Staple_DB::get();
		$db->setUsername('mvjobs')
			->setPassword('Claymore54')
			->setDb('mvjobs_primary')
			->setHost('localhost')
			->connect();
		
		
		$p = new Staple_Query_Select();
		$p->addColumn('name')
			->setTable('article_categories')
			->whereEqual('id', 'articles.cat', true);
			
		//Create the Query
		$q = new Staple_Query_Insert();
		$q
			->setTable('articles')
			->addData(array('id'=>1,
							'name'=>'Test',
							'quickname'=>'test',
							'summary'=>'This is a test and only a test.',
							'cat'=>2));
		
		echo "<p><h3>Query:</h3> ".$q->build()."</p>";
		
		//Execute the Query
		//$result = $db->query($q);
		
		echo "<h3>Object Dump:</h3><h4>Query:</h4>";
		
		Staple_Dev::Dump($q);
		echo "<h4>Result:</h4>";
		//Staple_Dev::Dump($result);
		echo "<h4>Error:</h4>";
		Staple_Dev::Dump($db->error);
	}
	
	public function circular()
	{
		echo "<h1>Inheritance Circular Reference Test</h1>";
		ini_set('display_errors',1);
		
		$a = new A();
		$b = new B();

		echo 'A->C(): '.$a->C()."<br>";
		echo 'B->C(): '.$b->C()."<br>";
		echo 'A->D(): '.$a->D()."<br>";
		echo 'B->D(): '.$b->D()."<br>";
		echo 'A->E(): '.$a->E()."<br>";
		echo 'B->E(): '.$b->E()."<br>";
		echo 'B::F(): '.B::F()."<br>";
	}
	
	public function linkedlist()
	{
		ini_set('display_errors', 'yes');
		set_time_limit(15);
		$list = new Staple_Data_LinkedList();
		$list->add('One');
		$list->add('Two');
		$list->add('Three');
		$list->add('Four');
		$list->add('Five');
		$list->add('Six');
		$list->add('Seven');
		$list->add('Eight');
		$list->add('Nine');
		
		echo 'List Length: '.$list->length().'<br>';
		
		echo 'Count: '.count($list).'<br>';
		
		//echo 'Current'.current($list).'<br>';
		
		$counter = 0;
		foreach($list as $key=>$item)
		{
			$counter++;
			echo 'Item '.$key.': '.$item."<br>\n";
		}
		
		$list[5]= 'Twenty';
		echo '<br>Item Key 5: '.$list[5].'<br>';
		unset($list[2]);
		echo '<br>Item Key 2: '.$list[2].'<br>';
		if(isset($list[15]))
		{
			echo 'Item 15 Exists<br>';
		}
		else
		{
			echo 'Item 15 Is Not Valid<br>';
		}
		if(isset($list[7]))
		{
			echo 'Item 7 Exists';
		}
		else
		{
			echo 'Item 7 Is Not Valid';
		}

		echo '<h3>The Final List</h3>';
		foreach($list as $key=>$item)
		{
			echo 'Item '.$key.': '.$item."<br>\n";
		}
	}
	
	public function phoneformat()
	{
		$number = '1.123.456.7890';
		
		$format = new Staple_Form_Filter_PhoneFormat();
		
		echo '<h2>Number Format Test</h2>';
		echo 'Original Number: '.$number.'<br>';
		echo 'Formatted Number: '.$format->filter($number);
		
	}
}