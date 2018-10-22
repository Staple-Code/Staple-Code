<?php
/**
 * Validates that a field does not equal a value.
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

class NotEqualValidator extends BaseValidator
{
    const DEFAULT_ERROR = 'Data matches an excluded value.';
    protected $equal;
    /**
     * @var bool
     */
    protected $strict;

    /**
     * NotEqualValidator constructor.
     * @param string $equal
     * @param bool $strict
     * @param null $userMessage
     */
    public function __construct($equal, bool $strict = false, $userMessage = NULL)
    {
        $this->equal = $equal;
        $this->strict = $strict;
        parent::__construct($userMessage);
    }

    /**
     * @param  mixed $data
     * @return  bool
     */
    public function check($data): bool
    {
        if ($this->strict === true)
        {
            if($this->equal !== $data)
            {
                return true;
            }
            else
            {
                $this->addError();
            }
        }
        else
        {
            if ($this->equal != $data)
            {
                return true;
            }
            else
            {
                $this->addError();
            }
        }
        return false;
    }
}