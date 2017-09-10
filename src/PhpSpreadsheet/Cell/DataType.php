<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class DataType
{
    /* Data types */
    const TYPE_STRING2 = 'str';
    const TYPE_STRING = 's';
    const TYPE_FORMULA = 'f';
    const TYPE_NUMERIC = 'n';
    const TYPE_BOOL = 'b';
    const TYPE_NULL = 'null';
    const TYPE_INLINE = 'inlineStr';
    const TYPE_ERROR = 'e';

    /**
     * List of error codes.
     *
     * @var array
     */
    private static $errorCodes = [
        '#NULL!' => 0,
        '#DIV/0!' => 1,
        '#VALUE!' => 2,
        '#REF!' => 3,
        '#NAME?' => 4,
        '#NUM!' => 5,
        '#N/A' => 6,
    ];

    /**
     * Get list of error codes.
     *
     * @return array
     */
    public static function getErrorCodes()
    {
        return self::$errorCodes;
    }

    /**
     * Check a string that it satisfies Excel requirements.
     *
     * @param mixed Value to sanitize to an Excel string
     * @param null|mixed $pValue
     *
     * @return mixed Sanitized value
     */
    public static function checkString($pValue)
    {
        if ($pValue instanceof RichText) {
            // TODO: Sanitize Rich-Text string (max. character count is 32,767)
            return $pValue;
        }

        // string must never be longer than 32,767 characters, truncate if necessary
        $pValue = StringHelper::substring($pValue, 0, 32767);

        // we require that newline is represented as "\n" in core, not as "\r\n" or "\r"
        $pValue = str_replace(["\r\n", "\r"], "\n", $pValue);

        return $pValue;
    }

    /**
     * Check a value that it is a valid error code.
     *
     * @param mixed Value to sanitize to an Excel error code
     * @param null|mixed $pValue
     *
     * @return string Sanitized value
     */
    public static function checkErrorCode($pValue)
    {
        $pValue = (string) $pValue;

        if (!isset(self::$errorCodes[$pValue])) {
            $pValue = '#NULL!';
        }

        return $pValue;
    }
}
