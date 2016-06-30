<?php
/**
 * OO Forms. This class is used as a base for extending or a direct mechanism to create
 * object-based HTML forms.
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

namespace Staple\Form;

use \Exception;
use \Staple\Error;
use \Staple\Config;
use \Staple\Encrypt;
use Staple\Exception\FormBuildException;
use \Staple\Form\ViewAdapters\ElementViewAdapter;
use Staple\Traits\Helpers;

class Form
{
	use Helpers;
	
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const ENC_APP = 'application/x-www-form-urlencoded';
	const ENC_FILE = 'multipart/form-data';
	const ENC_TEXT = 'text/plain';

	/**
	 * The action (form submittal) location.
	 * @var string
	 */
	protected $action;
	
	/**
	 * Holds the form's method.
	 * @var string
	 */
	protected $method = "POST";
	/**
	 * The name of the form. This name identifies the form in both the session and on the 
	 * website's HTML. It needs to be unique to any other forms on the website.
	 * @var string
	 */
	protected $name;
	/**
	 * Stores the EncType of the form.
	 * @var string
	 */
	protected $enctype;
	/**
	 * Set the HTML target attribute.
	 * @var string
	 */
	protected $target;
	/**
	 * An array that holds a list of callback functions to be called upon form validation.
	 * @var array
	 */
	protected $callbacks = array();
	
	/**
	 * Contains an array of errors from form validation
	 * @var array
	 */
	protected $errors = array();
	
	/**
	 * An array of FieldElement objects, that represent the form fields.
	 * @var FieldElement[]|CheckboxElement[]|SelectElement[]|CheckboxGroupElement[]|array
	 */
	public $fields = array();
	
	/**
	 * A boolean value that signifys a valid submission of the form.
	 * @var boolean
	 */
	private $createIdent = true;
	
	/**
	 * A long identifying value for the form. This is used to identify a valid form submission.
	 * @var string
	 */
	protected $identifier;
	
	/**
	 * Boolean value that signifys a submittal of the form on the last HTTP request.
	 * @var boolean
	 */
	private $submitted = false;
	
	/**
	 * Holds a list of the HTML classes to apply to the form tag.
	 * @var array
	 */
	protected $classes = array();
	
	/**
	 * Holds a title for the form.
	 * @var string
	 */
	protected $title;
	
	/**
	 * Holds the form layout name.
	 * @var string
	 */
	protected $layout;

	/**
	 * This holds the ElementViewAdapter object.
	 * @var ElementViewAdapter
	 */
	protected $elementViewAdapter;

	/**
	 * Dynamic datastore.
	 * @var array
	 */
	protected $_store = array();

	/**
	 * @param string $name
	 * @param string $action
	 */
	public function __construct($name = NULL, $action = NULL)
	{
		$this->_start();

		if(isset($name))
		{
			$this->name = $name;
		}
		if(isset($action))
		{
			$this->action = $action;
		}
		if(isset($this->name))
		{
			//check that the form was submitted.
			if(isset($_SESSION['Staple']['Forms'][$this->name]))
			{
				if(array_key_exists('ident', $_SESSION['Staple']['Forms'][$this->name]) && array_key_exists('ident', $_REQUEST))
				{
					if($_SESSION['Staple']['Forms'][$this->name]['ident'] == $_REQUEST['ident'])
					{
						$this->submitted = true;
					}
				}
			}
		}

		/**
		 * Loads selected elementViewAdapter from application.ini and verify given adapter is a class before loading
		 */
		if(Config::getValue('forms','elementViewAdapter', false) != '')
		{
			if(class_exists(Config::getValue('forms','elementViewAdapter')))
			{
				$this->makeElementViewAdapter(Config::getValue('forms','elementViewAdapter'));
			}
		}
		
		//create the form's identity field.
		if($this->createIdent === true)
		{
			$this->createIdentifier();
		}
	}
	
	/**
	 * Overloaded __set allows for dynamic addition of properties.
	 * @param string | int $key
	 * @param mixed $value
	 */
	public function __set($key,$value)
	{
		$this->_store[$key] = $value;
	}
	
	/**
	 * Retrieves a stored field element object or property.
	 * @param string $key
	 * @return FieldElement|mixed
	 */
	public function __get($key)
	{
		if(array_key_exists($key,$this->fields))
		{
			return $this->fields[$key];
		}
		elseif(array_key_exists($key,$this->_store))
		{
			return $this->_store[$key];
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * When sleeping in the session, do not save the current value of the submitted property.
	 * @return array
	 */
	public function __sleep()
	{
		return array_diff(array_keys(get_object_vars($this)), array('submitted'));
	}

	/**
	 * Upon wakeup, check to see if the form was submitted.
	 */
	public function __wakeup()
	{
		$this->checkSubmitted();
	}

	/**
	 * The toString magic method calls the forms build function to output the form to the website.
	 */
	public function __toString()
	{
		try {
			return $this->build();
		}
		catch (Exception $e)
		{
			$msg = '<p class=\"formerror\">Form Error....</p>';
			if(Config::getValue('errors', 'devmode'))
			{
				$msg .= '<p>'.$e->getMessage().'</p>';
			}
			return $msg;
		}
	}

	/**
	 * Boot function for initialization of forms that extend this class.
	 */
	public function _start()
	{
		
	}
	
	/**
	 * Creates the ident field, adds it to the form and save the value in the session. This is used
	 * to verify the form has been submitted and also aids in preventing CSRF form attacks.
	 */
	protected function createIdentifier()
	{
		$this->identifier = Encrypt::genHex(32);
		$ident = new HiddenElement('ident');
		$ident->setValue($this->identifier)
			->setReadOnly();
		$this->addField($ident);

		//Set the identifier into the session.
		$_SESSION['Staple']['Forms'][$this->name]['ident'] = $this->identifier;
	}

	/**
	 * Remove the identifier from the form.
	 * @return $this
	 */
	public function disableIdentifier()
	{
		//Unset the ident variable, if set
		if(isset($this->identifier))
			unset($this->identifier);

		//Set createIdent to false.
		$this->createIdent = false;

		//Remove from the field list, if exists.
		if(isset($this->fields['ident']))
			unset($this->fields['ident']);

		//Remove from the session, if set.
		if(isset($_SESSION['Staple']['Forms'][$this->name]['ident']))
			unset($_SESSION['Staple']['Forms'][$this->name]['ident']);

		//Return current object
		return $this;
	}
	
	/**
	 * Adds a field to the form from an already instantiated form element.
	 * @param FieldElement $field
	 * @return $this
	 */
	public function addField(FieldElement $field)
	{
		$args = func_get_args();
		foreach($args as $newField)
		{
			if($newField instanceof FileElement)
			{
				$this->setEnctype(self::ENC_FILE);
			}
			if($newField instanceof FieldElement)
			{
				$this->fields[$newField->getName()] = $newField;
		                if(isset($this->elementViewAdapter))
		                {
		                    $this->fields[$newField->getName()]->setElementViewAdapter($this->getElementViewAdapter());
		                }
			}

		}
		return $this;
	}
	
	/**
	 * Accepts an associative array of fields=>values to apply to the form elements.
	 * @param array $data
	 * @return $this
	 */
	public function addData(array $data)
	{
		$this->addDataToTarget($data,$this->fields);

		return $this;
	}

	/**
	 * @param array $data
	 * @param FieldElement[] | array $target
	 */
	private function addDataToTarget(array $data, $target)
	{
		foreach($target as $fieldName=>$obj)
		{
			if(is_array($obj))
			{
				if(isset($data[$fieldName]))
					if(is_array($data[$fieldName]))
						$this->addDataToTarget($data[$fieldName],$obj);
			}
			elseif(array_key_exists($fieldName, $data))
			{
				$obj->setValue($data[$fieldName]);
			}
			elseif($obj instanceof CheckboxGroupElement)
			{
				$boxes = $obj->getBoxes();
				foreach($boxes as $chk)
				{
					if(array_key_exists($chk->getName(), $data))
					{
						$chk->setValue($data[$chk->getName()]);
					}
					else
					{
						$chk->setValue(0);
					}
				}
			}
			else
			{
				//Checkbox Fix
				if($obj->isDisabled() === false && $obj instanceof CheckboxElement)
				{
					$obj->setValue(NULL);
				}
			}
		}
	}
	
	/**
	 * Returns an associative array of the field values with the field names as the keys, including
	 * the identity field.
	 * @return array
	 */
	public function exportFormData()
	{
		return $this->fieldData($this->fields);
	}

	/**
	 * Recursively pulls field data.
	 * @param array $start
	 * @return array
	 */
	private function fieldData(array $start)
	{
		$data = array();
		/**
		 * @var string $name
		 * @var FieldElement|array $field
		 */
		foreach($start as $name=>$field)
		{
			if(is_array($field))
				$data[$name] = $this->fieldData($field);
			else
				$data[$field->getName()] = $field->getValue();
		}
		return $data;
	}
	
	/**
	 * Returns the value of $this->submitted
	 * @return bool
	 */
	public function wasSubmitted()
	{
		if($this->submitted == false)
		{
			return $this->checkSubmitted();
		}
		return $this->submitted;
	}

	/**
	 * Checks for the flags for form submission
	 * @return bool
	 */
	private function checkSubmitted()
	{
		if (isset($this->name) && isset($_SESSION['Staple']['Forms'][$this->name]['ident']) && isset($_REQUEST['ident']))
		{
			if ($_SESSION['Staple']['Forms'][$this->name]['ident'] == $_REQUEST['ident'])
			{
				$this->submitted = true;
			}
		}
		return $this->submitted;
	}
	
	/**
	 * Adds an HTML class to the form.
	 * @param string $class
	 * @return $this
	 */
	public function addClass($class)
	{
		if(!in_array($class,$this->classes))
		{
			$this->classes[] = $class;
		}
		return $this;
	}
	
	/**
	 * Removes an HTML class from the form.
	 * @param string $class
	 * @return $this
	 */
	public function removeClass($class)
	{
		if(($key = array_search($class,$this->classes)) !== false)
		{
			unset($this->classes[$key]);
		}
		return $this;
	}
	
	/**
	 * Checks that the specified field exists on the form and that it is instantiated.
	 * @param string $field
	 * @return boolean
	 */
	public function fieldExists($field)
	{
		if(array_key_exists($field, $this->fields))
		{
			if($this->fields[$field] instanceof FieldElement)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @todo complete the javascript validators.....
	 * 
	 */
	public function clientJS()
	{
		
	}
	
	public function clientJQuery()
	{
		$script = <<<JS
var {$this->name}validated = false;
$(function (){
	$('#{$this->name}_form').submit(function (){
	var errors = [];
JS;
		
		foreach($this->fields as $field)
		{
			if($field instanceof FieldElement)
			{
				if($field->isRequired())
				{
					$script .= $field->clientJQuery();
				}
			}
			elseif(is_array($field))	//Limited Recursion here.
			{
				foreach($field as $subField)
				{
					if($subField instanceof FieldElement)
					{
						if($subField->isRequired())
						{
							$script .= $subField->clientJQuery();
						}
					}
					else
					{
						throw new Exception('Form Error', Error::FORM_ERROR);
					}
				}
			}
			else
			{
				throw new Exception('Form Error', Error::FORM_ERROR);
			}
		}
		
		
		$script .= <<<JS
	if(errors.length > 0)
	{
		var msg  = 'Please correct these form errors:\\n';
		var count = 0;
		for(var x in errors)
		{
			count++;
			if(count <= 10)
			{
				msg += '\\n'+errors[x];
			}
		}
		if(count > 10)
		{
			msg += '\\nAnd '+count+' more...';
		}
		alert(msg);
		//jQuery UI Dialog
		//$('<div class="form_validation_dialog" title="Form Errors">'+msg+'</div>').dialog({modal:true, buttons: {'Ok': function(){ $(this).dialog('close'); }}});
		{$this->name}validated = false;
		return false;
	}
	else
	{
		{$this->name}validated = true;
	}
	})
});
JS;
		$script .= "";
		$script .= "";
		
		//$script = "$('#{$this->name}_form').submit(false);\n";
		return $script;
	}
	
	/**
	 * Runs the validators on each field and checks for the completion of required fields.
	 * 
	 * @throws Exception
	 */
	public function validate()
	{
		$this->clearErrors();
		
		//Process validation callbacks.
		$valErrors = $this->validateCallbacks($this->callbacks);
		
		//Process all validation fields.
		$fieldErrors = $this->validateFields($this->fields);

		//Merge all of the errors together
		$errors = array_merge($this->errors, $valErrors, $fieldErrors);

		//Set the validation errors
		$this->setErrors($errors);
		
		//Check for errors.
		if(count($this->errors) > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Process the supplied array of validation callbacks and return errors.
	 * @param array $callbacks
	 * @return array
	 */
	public function validateCallbacks($callbacks)
	{
		$errors = [];
		foreach($callbacks as $func)
		{
			try{
				$result = call_user_func_array($func['func'],$func['params']);
			}
			catch (Exception $e)
			{
				$result = false;
			}

			if(is_array($result))
			{
				$errors[] = array('label'=>'Additional Form Validation','errors'=>array($result));
			}
			else
			{
				$result = (bool)$result;
				if($result === false)
				{
					$errors[] = array('label'=>'Additional Form Validation','errors'=>array(array('Form Validation Returned False.')));
				}
			}
		}

		return $errors;
	}

	/**
	 * Run validation on supplied array of fields. Runs recursively for nested arrays.
	 * @param array $fields
	 * @return array
	 * @throws Exception
	 */
	public function validateFields($fields)
	{
		$errors = [];
		foreach($fields as $key=>$field)
		{
			if($field instanceof FieldElement)
			{
				if(!$field->isValid())
				{
					if($field->isRequired())
					{
						$errors[$key] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
					}
					elseif($field->getValue() != '')
					{
						//A few extra steps to handle File Uploads
						if($field instanceof FileElement)
						{
							if(is_array($field->getValue()))
							{
								$file = $field->getValue();
								if(isset($file['error']))
								{
									if($file['error'] != UPLOAD_ERR_NO_FILE)
									{
										$errors[$key] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
									}
								}
								else
								{
									$errors[$key] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
								}
							}
							else
							{
								$errors[$key] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
							}
						}
						else
						{
							$errors[$key] = array('label'=>$field->getLabel(),'errors'=>$field->getErrors());
						}
					}
				}
			}
			elseif(is_array($field))        //Parse Field Validation Recursively
			{
				$errArray = $this->validateFields($field);
				if(count($errArray) > 0)
					$errors[$key] = $errArray;
			}
			else
			{
				throw new FormBuildException('Form Error', Error::FORM_ERROR);
			}
		}

		return $errors;
	}
	
	/**
	 * Add a single error to the form errors
	 * @param string $label
	 * @param string $msg
	 */
	public function addError($label,$msg)
	{
		$this->errors[] = array('label'=>$label,'errors'=>array(array($msg)));
	}
	
	/**
	 * @return array $errors
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Set the error array
	 * @param array $errors
	 * @return $this
	 */
	protected function setErrors(array $errors)
	{
		$this->errors = $errors;
		return $this;
	}

	/**
	 * Clear out the form errors before validating.
	 */
	protected function clearErrors()
	{
		$this->errors = array();
		return $this;
	}

	/**
	 * Adds a callback function to the validation stack. The function can be any standard callback, including an annonymous function.
	 * A callback function must return a boolean true on success, and boolean false or an array of errors on failure.
	 *
	 * @param callback $func
	 * @param array $params
	 * @return $this
	 */
	public function addValidationCallback($func,$params = array())
	{
		array_push($this->callbacks, array('func'=>$func,'params'=>$params));
		return $this;
	}
	
	/*----------------------------------------Getters and Setters----------------------------------------*/
	
	/**
	 * Sets the form action location
	 * @param string $action
	 * @return Form
	 */
	public function setAction($action)
	{
		$this->action = $action;
		return $this;
	}
	
	/**
	 * Returns the action location of the form.
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}
	
	/**
	 * Sets the method for the form. Only accepts GET and POST. POST is the default.
	 * @param string $method
	 * @return Form
	 */
	public function setMethod($method)
	{
		if(strtoupper($method) == "GET")
		{
			$this->method = "GET";
		}
		else
		{
			$this->method = "POST";
		}
		return $this;
	}
	
	/**
	 * Returns the method, either GET or POST
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}
	
	/**
	 * Sets the name of the form
	 * @param string $name
	 * @return Form
	 */
	public function setName($name)
	{
		$this->name = str_replace(' ','_',$name);
		return $this;
	}
	
	/**
	 * Returns the name of the form.
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @return string $enctype
	 */
	public function getEnctype()
	{
		return $this->enctype;
	}

	/**
	 * Sets the ENCTYPE attribute value for this form.
	 * @param string $enctype
	 * @return $this
	 */
	public function setEnctype($enctype)
	{
		switch($enctype)
		{
			case self::ENC_APP:
			case self::ENC_FILE:
				case self::ENC_TEXT:
				$this->enctype = $enctype;
				break;
		}
		return $this;
	}

	/**
	 * @return string $title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return string $layout
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * @param string $layout
	 * @return $this
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;
		return $this;
	}

	/**
	 * @return ElementViewAdapter
	 */
	public function getElementViewAdapter()
	{
		return $this->elementViewAdapter;
	}

	/**
	* Make a view adapter from the string name
	* @param $viewAdapterString
	* @return $this
	*/
	protected function makeElementViewAdapter($viewAdapterString)
	{
		$obj = new $viewAdapterString();
		if($obj instanceof ElementViewAdapter)
		{
		    $this->setElementViewAdapter($obj);
		}

		//Attach to all of the fields in the object
		foreach($this->fields as $field)
		{
			$field->setElementViewAdapter($obj);
		}

		return $this;
	}

	/**
	 * Set the view adapter to use when building the form.
	 * @param ElementViewAdapter $elementViewAdapter
	 * @return $this
	 */
	public function setElementViewAdapter(ElementViewAdapter $elementViewAdapter)
	{
		$this->elementViewAdapter = $elementViewAdapter;

		//Attach to all of the fields in the object
		foreach($this->fields as $field)
		{
			$field->setElementViewAdapter($elementViewAdapter);
		}

		return $this;
	}

	/**
	 * @param string $title
	 * @return $this
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	/**
	 * @return string $target
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * @param string $target
	 * @return $this
	 */
	public function setTarget($target)
	{
		$this->target = $target;
		return $this;
	}

	/**
	 * Returns the value for the names field.
	 * @param $fieldName
	 * @return array|bool|null|string
	 */
	public function getFieldValue($fieldName)
	{
		if(array_key_exists($fieldName,$this->fields))
		{
			if($this->fields[$fieldName] instanceof FieldElement)
			{
				return $this->fields[$fieldName]->getValue();
			}
		}
		return NULL;
	}
	
	/*----------------------------------------Builders----------------------------------------*/

	/**
	 * Returns a string containing the form start and structure tags.
	 * @return string
	 */
	public function formstart()
	{
		$buf = '';
		$buf .= "\n".'<form name="'.$this->name.'" id="'.$this->name.'_form" action="'.$this->action.'" method="'.$this->method.'"';
		if(isset($this->enctype))
		{
			$buf .= ' enctype="'.$this->enctype.'"';
		}
		if(isset($this->target))
		{
			$buf .= ' target="'.$this->target.'"';
		}
		if(count($this->classes) > 0)
		{
			$buf .= ' class="';
			$classString = '';
			foreach($this->classes as $class)
			{
				$classString .= $class.' ';
			}
			$buf .= trim($classString);
			$buf .= '"';
		}
	        else
	        {
	            $classString = '';
	        }
		$buf .= ">\n";
		$buf .= '<div id="'.$this->name.'_div"';
		if(count($this->classes) > 0)
		{
			$buf .= ' class="'.trim($classString).'"';
		}
		$buf .= ">\n";
		return $buf;
	}

	/**
	 * Returns a string containing the form end tags and the identifier field.
	 * @return string
	 */
	public function formend()
	{
		$buf = "\n";
		if(array_key_exists('ident', $this->fields))
		{
			if($this->fields['ident'] instanceof FieldElement)
			{
				$buf .= $this->fields['ident']->build();
			}
		}
		$buf .= "\n</div>\n</form>\n";
		return $buf;
	}
	
	/**
	 * @deprecated
	 * @return string
	 */
	public function title()
	{
		return $this->title;
	}

	/**
	 * Build out all of the form fields, excluding the identifier field.
	 * @return string
	 */
	public function fields()
	{
		$buf = '';
		foreach($this->fields as $field)
		{
			if($field->getName() != 'ident')
			{
				$buf .= $field->build();
			}
		}
		return $buf;
	}

	/**
	 * Constructs and echos the HTML for the form and all of its elements.
	 * @return string
	 * @throws Exception
	 */
	public function build()
	{
		$buf = '';
		if(isset($this->layout))
		{
			$layoutLocation = FORMS_ROOT.'layouts/'.basename($this->layout).'.phtml';
			if(file_exists($layoutLocation))
			{
				ob_start();
				include $layoutLocation;
				$buf = ob_get_contents();
				ob_end_clean();
			}
			else 
			{
				throw new Exception('Unable to load form layout.', Error::FORM_ERROR);
			}
		}
		else
		{
			$buf .= $this->formstart();
			$buf .= $this->title();
			$buf .= $this->fields();
			$buf .= $this->formend();
		}
		return $buf;
	}

    /*---------------------------------------SHORT FORM CREATION METHODS---------------------------------------*/

    /**
     * A factory function to encapsulate the creation of form objects.
     * @param string $name
     * @param string $action
     * @param string $method
     * @return Form
     */
    public static function create($name, $action = NULL, $method = self::METHOD_POST)
    {
        $inst = new self($name,$action);
        $inst->setMethod($method);
        return $inst;
    }

    /**
     * Short method for creating a text element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return TextElement
     */
    public static function textElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new TextElement($name, $label, $id, $attributes);
    }

    /**
     * Short method for creating a text element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return TextElement
     */
    public static function passwordElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new PasswordElement($name, $label, $id, $attributes);
    }

    /**
     * Short method for creating a textarea element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return TextareaElement
     */
    public static function textareaElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new TextareaElement($name, $label, $id, $attributes);
    }

    /**
     * Short method for creating a radio element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return RadioElement
     */
    public static function radioElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new RadioElement($name, $label, $id, $attributes);
    }

    /**
     * Short method for creating a select element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return SelectElement
     */
    public static function selectElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new SelectElement($name, $label, $id, $attributes);
    }

    /**
     * Short method for creating a checkbox element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return CheckboxElement
     */
    public static function checkboxElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new CheckboxElement($name, $label, $id, $attributes);
    }

    /**
     * Short method for creating a select element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return SubmitElement
     */
    public static function submitElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new SubmitElement($name, $label, $id, $attributes);
    }

    /**
     * Short method for creating a button element.
     * @param string $name
     * @param string $value
     * @param string $id
     * @param array $attributes
     * @return ButtonElement
     */
    public static function buttonElement($name, $value = NULL, $id = NULL, array $attributes = array())
    {
        return new ButtonElement($name, $value, $id, $attributes);
    }

    /**
     * Short method for creating a file element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return FileElement
     */
    public static function fileElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new FileElement($name, $label, $id, $attributes);
    }

    /**
     * Short method for creating a hidden element.
     * @param string $name
     * @param string $value
     * @param string $id
     * @param array $attributes
     * @return HiddenElement
     */
    public static function hiddenElement($name, $value = NULL, $id = NULL, array $attributes = array())
    {
        return new HiddenElement($name, $value, $id, $attributes);
    }

    /**
     * Short method for creating a image element.
     * @param string $name
     * @param string $label
     * @param string $id
     * @param array $attributes
     * @return ImageElement
     */
    public static function imageElement($name, $label = NULL, $id = NULL, array $attributes = array())
    {
        return new ImageElement($name, $label, $id, $attributes);
    }
}