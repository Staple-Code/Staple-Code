<?php
/**
 * A base interface for validation in the framework.
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

namespace Staple\Validate;

interface IValidator
{
    /**
     * Creates the validator and sets and options user defined error message.
     * @param string $userMessage
     * @return IValidator
     */
    public static function create(string $userMessage = NULL): IValidator;

    /**
     * Clears all the errors in the errors array.
     */
    public function clearErrors(): IValidator;

    /**
     * Adds a custom error or adds the default error to the errors array.
     * @param string $error
     * @return IValidator
     */
    public function addError(string $error = null): IValidator;

    /**
     * Return the errors as a string.
     * @return string
     */
    public function getErrorsAsString(): string;

    /**
     * Set a different error message text.
     * @param string $userMessage
     */
    public function setUserErrorMessage(string $userMessage): IValidator;

    /**
     * Return the errors array
     * @return array
     */
    public function getErrors(): array;

    /**
     * Returns the name of the validator
     * @return string
     */
    public function getName(): string;

    /**
     *
     * Returns a boolean true or false on success or failure of the validation check.
     * @param mixed $data
     * @return bool
     */
    public function check($data): bool;
}