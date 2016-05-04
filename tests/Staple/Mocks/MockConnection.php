<?php
/**
 * Mock Connection object for Staple Tests
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
namespace Staple\Tests;

use Staple\Query\Connection;
use PDO;

class MockConnection extends Connection
{
    public function __construct($driver = NULL)
    {
        if(isset($driver))
            $this->setDriver($driver);
    }

    public function query($sql)
    {
        return true;
    }

    /**
     * Mock quote method
     * @todo add more types and escaping features here.
     * @param string $input
     * @param int $parameter_type
     * @return string
     */
    public function quote($input,$parameter_type = PDO::PARAM_STR)
    {
        switch($this->getDriver())
        {
            case Connection::DRIVER_MYSQL:
                return '\''.$input.'\'';
            default:
                return $input;
        }
    }
}