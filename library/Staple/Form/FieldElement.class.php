<?php
/**
 * The root element class. All other form elements must inherit from this class.
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

use \Staple\Error;
use \Exception;
use Staple\Form\ViewAdapters\ElementViewAdapter;

abstract class FieldElement
{
	use \Staple\Traits\Helpers;

	/**
	 * Name of the field on the form.
	 * @var string
	 */
	protected $name;
	/**
	 * Form field's label.
	 * @var string
	 */
	protected $label;
	/**
	 * The field's current value.
	 * @var string
	 */
	protected $value;
	/**
	 * An array of HTML classes for the form field.
	 * @var array
	 */
	protected $classes = array();
	/**
	 * Form field ID, uses the form name if the ID is not specified.
	 * @var string
	 */
	protected $id;
	/**
	 * Holds instructions for completing the form field.
	 * @var string
	 */
	protected $instructions;
	/**
	 * An array that holds extra HTML attributes.
	 * @var array
	 */
	protected $attrib = array();
	/**
	 * Sets the field to be required.
	 * @var boolean
	 */
	protected $required = false;
	/**
	 * Holds the true|false readOnly value.
	 * @var boolean
	 */
	protected $readOnly = false;

	/**
	 * Hold the true|false disabled value.
	 * @var boolean
	 */
	protected $disabled = false;
	/**
	 * An array of filters that will be applied to any form values.
	 * @var array
	 */
	protected $filters = array();
	/**
	 * An array that holds the validator objects assigned to the form field.
	 * @var FieldValidator[]
	 */
	protected $validators = array();
	/**
	 * An array that holds the validation error messages.
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Contains instance of ElementViewAdapter handed down from Form
	 * @var ElementViewAdapter
	 */
	protected $elementViewAdapter;

	/**
	 * Form field constructor. Requires a name, has an optional label, id and attribute array.
	 *
	 * @param string $name
	 * @param string $label
	 * @param string $id
	 * @param array $attrib
	 */
	public function __construct($name, $label = NULL, $id = NULL, array $attrib = array())
	{
		$this->setName($name);                //Name for the form field
		$this->setLabel($label);            //Label that the user sees
		if (isset($id))                        //ID for the form field
		{
			$this->setId($id);
		}
		else
		{
			$this->setId($name);
		}
		foreach ($attrib as $key => $value)        //Additional HTML Attributes
		{
			$this->addAttrib($key, $value);
		}
	}

	/**
	 * Class overload either calls a getter for a protected value or gets the value from the attribute array.
	 *
	 * @param string $name
	 * @return null|string
	 */
	public function __get($name)
	{
		$method = 'get' . $name;
		if (method_exists(get_class($this), $method))
		{
			return $this->$method();
		}
		else
		{
			if (array_key_exists($name, $this->attrib))
			{
				return $this->attrib[$name];
			}
			else
			{
				return NULL;
			}
		}
	}

	/**
	 * Class overload either calls a setter for a protected value or sets that value within the attributes array.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (method_exists(get_class($this), $method))
		{
			$this->$method($value);
		}
		else
		{
			$this->attrib[$name] = $value;
		}
	}

	/**
	 * Calls the field->build() function.
	 */
	public function __toString()
	{
		try
		{
			return $this->build();
		}
		catch (Exception $e)
		{
			return '<p>Field Error...</p>';
		}
	}

	/**
	 * Clone validators and filters so that they don't overlap;
	 */
	public function __clone()
	{
		$vals = array();
		foreach ($this->validators as $key => $val)
		{
			$vals[$key] = clone $val;
		}
		$this->validators = $vals;

		$filts = array();
		foreach ($this->filters as $key => $fil)
		{
			$filts[$key] = clone $fil;
		}
		$this->filters = $filts;
	}

	/**
	 * A factory function to create form fields.
	 *
	 * @param string $name
	 * @param string $labelOrValue
	 * @param string $id
	 * @param array $attributes
	 * @return $this
	 */
	public static function create($name, $labelOrValue = NULL, $id = NULL, array $attributes = array())
	{
		return new static($name, $labelOrValue, $id, $attributes);
	}

	/*
	 * -------------------------------------VALIDATION FUNCTIONS-------------------------------------
	 */

	/**
	 * Adds a field filter to the form field.
	 * @param FieldFilter $filter
	 * @return $this
	 */
	public function addFilter(FieldFilter $filter)
	{
		$this->filters[$filter->getName()] = $filter;
		return $this;
	}

	/**
	 * Adds a field validator to the form field.
	 * @param FieldValidator $validator
	 * @return $this
	 */
	public function addValidator(FieldValidator $validator)
	{
		$this->validators[] = $validator;
		return $this;
	}

	/**
	 * Removes all validators from  a form field.
	 */
	public function clearValidators()
	{
		$this->validators = array();
		return $this;
	}

	/**
	 * Sets the field to be required to be completed to be valid. The optional parameter also allows you
	 * to set required to false using this function.
	 * @param bool $bool
	 * @return $this
	 */
	public function setRequired($bool = true)
	{
		$this->required = (bool)$bool;
		if ($bool === true)
		{
			$this->addClass('form_required');
		}
		return $this;
	}

	/**
	 * Sets the field to not be required.
	 * @return $this
	 */
	public function setNotRequired()
	{
		$this->required = false;
		return $this;
	}

	/**
	 * Returns a boolean value of whether the field is required.
	 * @return bool
	 */
	public function isRequired()
	{
		return (bool)$this->required;
	}

	/**
	 * Returns an array of the errors that occurred during form validation.
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Clears the error list on the field element.
	 * @return $this
	 */
	protected function clearErrors()
	{
		$this->errors = [];
		return $this;
	}

	/**
	 * Checks the form field against any validators to confirm valid data was entered into the form.
	 * If no validators are associated with the field, it is assumed that no validation is required
	 * and the field will always return valid. If the field is required then a value must be filled
	 * into the field for it to be valid, even with no validators.
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		foreach ($this->validators as $val)
		{
			$val->clearErrors();
			$this->clearErrors();
			if (!$val->check($this->value))
			{
				$this->errors[$val->getName()] = $val->getErrors();
			}
		}

		if (count($this->errors) == 0)
		{
			if ($this->isRequired() && $this->getValue() !== NULL && $this->getValue() !== '')
			{
				return true;
			}
			else
			{
				if (!$this->isRequired())
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * This function queries all of the validators for javascript to verify their data.
	 * @throws Exception
	 * @return string
	 */
	public function clientJQuery()
	{
		$script = '';
		foreach ($this->validators as $val)
		{
			if ($val instanceof FieldValidator)
			{
				$script .= $val->clientJQuery(get_class($this), $this);
			}
			else
			{
				throw new Exception('Form Error', Error::FORM_ERROR);
			}
		}
		return $script;
	}

	/**
	 * This function queries all of the validators for javascript to verify their data.
	 * @throws Exception
	 * @return string
	 */
	public function clientJS()
	{
		$script = '';
		foreach ($this->validators as $val)
		{
			if ($val instanceof FieldValidator)
			{
				$script .= $val->clientJS(get_class($this), $this);
			}
			else
			{
				throw new Exception('Form Error', Error::FORM_ERROR);
			}
		}
		return $script;
	}

	/*
	 * -------------------------------------SET/GET FUNCTIONS-------------------------------------
	 */

	/**
	 * Sets the name.
	 * @param string $insert
	 * @return $this
	 */
	public function setName($insert)
	{
		$this->name = $insert;
		return $this;
	}

	/**
	 * Gets the name.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the field label.
	 * @param string $insert
	 * @param bool $noescape
	 * @return $this
	 */
	public function setLabel($insert, $noescape = FALSE)
	{
		if ($noescape === true)
		{
			$this->label = $insert;
		}
		else
		{
			$this->label = $this->escape($insert);
		}
		return $this;
	}

	/**
	 * Gets the field label.
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Sets the field value, if field is not read only.
	 * @param mixed $insert
	 * @throws Exception
	 * @return $this
	 */
	public function setValue($insert)
	{
		if (!$this->isReadOnly())
		{
			//Process Filters
			foreach ($this->filters as $name => $filter)
			{
				if ($filter instanceof FieldFilter)
				{
					$insert = $filter->filter($insert);
				}
				else
				{
					throw new Exception('Filter Error', Error::FORM_ERROR);
				}
			}

			$this->value = $insert;
		}
		return $this;
	}

	/**
	 * Gets the form fields value.
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string $id
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string $instructions
	 */
	public function getInstructions()
	{
		return $this->instructions;
	}

	/**
	 * @param string $instructions
	 * @return $this
	 */
	public function setInstructions($instructions)
	{
		$this->instructions = $instructions;
		return $this;
	}

	/**
	 * Alias for setInstructions Method
	 * @param string $instructions
	 * @return $this
	 */
	public function addInstructions($instructions)
	{
		$this->setInstructions($instructions);
		return $this;
	}

	/**
	 * Set instance passed from Form into element
	 * @param ElementViewAdapter $adapter
	 * @return $this
	 */
	public function setElementViewAdapter(ElementViewAdapter $adapter)
	{
		$this->elementViewAdapter = $adapter;
		return $this;
	}

	/**
	 * Return instance of elementView adapter contained in $elementViewAdapter
	 * @return ElementViewAdapter
	 */
	public function getElementViewAdapter()
	{
		return $this->elementViewAdapter;
	}

	/**
	 * Sets $readOnly to true
	 * @return $this
	 */
	public function setReadOnly()
	{
		$this->readOnly = true;
		return $this;
	}

	/**
	 * Returns the readOnly value.
	 * @return boolean
	 */
	public function isReadOnly()
	{
		return $this->readOnly;
	}

	/**
	 * @return bool $disabled
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}

	/**
	 * @param boolean $disabled
	 * @return $this
	 */
	public function setDisabled($disabled = true)
	{
		$this->disabled = (bool)$disabled;
		return $this;
	}

	/**
	 *
	 * Adds an attribute to the attributes array. $attrib is the key or attribute name, and $value is its value.
	 * @param string $attrib
	 * @param string $value
	 * @return $this
	 */
	public function addAttrib($attrib, $value)
	{
		$this->attrib[$attrib] = $value;
		return $this;
	}

	/**
	 * Add a CSS class to the Element
	 * The $onlyTags parameter allows a user to specify the tag only be applied to a specific HTML tag within the element.
	 * @param string $class
	 * @param array $onlyTags - Not implemented yet
	 * @return $this
	 */
	public function addClass($class, array $onlyTags = array())
	{
		//Check if this new class will be tag specific
		if (count($onlyTags) > 0)
		{
			//Loop through each tag in the $onlyTags array
			foreach ($onlyTags as $tag)
			{
				//Check if the tag even exists.
				if (!isset($this->classes[$tag]))
				{
					//Add the class to its own array for the tag.
					$this->classes[$tag][] = $class;
				}
				elseif (in_array($class, $this->classes[$tag]) === false)	//Check if the class already exists.
				{
					//Add the class to its own array for the tag.
					$this->classes[$tag][] = $class;
				}
			}
		}
		else
		{
			//Check that the class does not exist in the global array.
			if (in_array($class, $this->classes) === false)
			{
				//Add the class string to the main class array as a numeric key.
				$this->classes[] = $class;
			}
		}
		return $this;
	}

	/**
	 * Remove a class from the Element, specify the optional param to remove a tag specific class
	 * @param string $class
	 * @param string $tag
	 * @return $this
	 */
	public function removeClass($class, $tag = NULL)
	{
		if ($tag != null)
		{
			if (isset($this->classes[$tag]))
			{
				if (($key = array_search($class, $this->classes[$tag])) !== false)
				{
					unset($this->classes[$tag][$key]);
				}
			}
		}
		elseif (($key = array_search($class, $this->classes)) !== false)
		{
			unset($this->classes[$key]);
		}
		return $this;
	}

	/**
	 * Returns the classes array as a string
	 * @param string $tag
	 * @return string
	 */
	public function getClassString($tag = NULL)
	{
		if (count($this->classes) >= 1)
		{
			$classTemp = ' class="';
			foreach ($this->classes as $key => $class)
			{
				if (is_string($class))
				{
					$classTemp .= $this->escape($class) . ' ';
				}
				elseif ($tag != NULL && $key == $tag)        //Check for classes that apply only to this tag.
				{
					foreach ($class as $subClass)
					{
						//Apply tag only classes
						$classTemp .= $this->escape($subClass) . ' ';
					}
				}
			}
			$classTemp = substr($classTemp, 0, strlen($classTemp) - 1) . '"';
			return $classTemp;
		}
		else
		{
			//Return null string when there are no classes to apply.
			return '';
		}
	}

	/**
	 * Returns the classes array.
	 * @return array[string]
	 */
	public function getCSSClasses()
	{
		return $this->classes;
	}

	/**
	 * Returns the CSS classes applied to the field
	 * @return array[string]
	 */
	public function getClasses()
	{
		return $this->classes;
	}

	/**
	 * Returns all the attributes formatted as and HTML string.
	 * @param string $tag
	 * @return string
	 */
	public function getAttribString($tag = NULL)
	{
		$attribs = '';
		if ($this->isDisabled())
		{
			$attribs .= ' disabled';
		}
		if ($this->isReadOnly())
		{
			$attribs .= ' readOnly';
		}
		$attribs .= $this->getClassString($tag);
		foreach ($this->attrib as $key => $value)
		{
			$attribs .= ' ' . $this->escape($key) . '="' . $this->escape($value) . '"';
		}
		return $attribs;
	}

	/*
	 * -------------------------------------FORM FUNCTIONS-------------------------------------
	 */

	/**
	 * Build the field instructions
	 */
	public function instructions()
	{
		if (strlen($this->instructions) > 0)
		{
			return '	<p class="field_instructions">' . $this->escape($this->instructions) . '</p>';
		}
		else
		{
			return '';
		}
	}

	/**
	 * Build the field label
	 */
	abstract public function label();

	/**
	 * Build the field itself
	 */
	abstract public function field();

	/**
	 * Build the field using a layout, or with the default build.
	 */
	abstract public function build($fieldView = NULL);

	/*
	 * @todo add method to add custom field view and add property to hold field view name
	 */
}