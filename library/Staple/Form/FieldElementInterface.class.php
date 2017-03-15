<?php
/**
 * Interface for form fields.
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


interface FieldElementInterface
{
	//Factory Method
	public static function create($name, $label = NULL, $id = NULL, array $attrib = array());

	//Getters and Setters
	public function setName($insert);
	public function getName();
	public function setLabel($insert, $noEscape = FALSE);
	public function getLabel();
	public function setValue($insert);
	public function getValue();
	public function getId();
	public function setId($id);
	public function getInstructions();
	public function setInstructions($instructions);
	public function setReadOnly();
	public function isReadOnly();
	public function isDisabled();
	public function setDisabled($disabled = true);

	//Attributes and Classes
	public function addAttrib($attrib, $value);
	public function addClass($class, array $onlyTags = array());
	public function removeClass($class, $tag = NULL);
	public function getClassString($tag = NULL);
	public function getClasses();
	public function getAttribString($tag = NULL);

	//Filters and Validators
	public function addFilter(FieldFilter $filter);
	public function addValidator(FieldValidator $validator);
	public function clearValidators();
	public function getErrors();
	public function isValid();

	//Required or Not
	public function setRequired($bool = true);
	public function setNotRequired();

	//Field Build Methods
	public function addToForm(Form $form);
	public function instructions();
	public function label();
	public function field();
	public function build();

}