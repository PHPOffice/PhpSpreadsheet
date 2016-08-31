<?php

namespace PhpOffice\PhpSpreadsheet;

/**
 * Autoloader for PhpSpreadsheet classes
 *
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class Autoloader
{
    /**
     * Register the Autoloader with SPL
     */
    public static function register()
    {
        if (function_exists('__autoload')) {
            // Register any existing autoloader function with SPL, so we don't get any clashes
            spl_autoload_register('__autoload');
        }
        // Register ourselves with SPL
        return spl_autoload_register([\PhpOffice\PhpSpreadsheet\Autoloader::class, 'load'], true, true);
    }

    /**
     * Autoload a class identified by name
     *
     * @param  string  $className  Name of the object to load
     */
    public static function load($className)
    {
        $prefix = 'PhpOffice\\PhpSpreadsheet\\';
        if ((class_exists($className, false)) || (strpos($className, $prefix) !== 0)) {
            // Either already loaded, or not a PhpSpreadsheet class request
            return false;
        }

        $classFilePath = __DIR__ . DIRECTORY_SEPARATOR .
            'PhpSpreadsheet' . DIRECTORY_SEPARATOR .
            str_replace([$prefix, '\\'], ['', '/'], $className) .
            '.php';

        if ((file_exists($classFilePath) === false) || (is_readable($classFilePath) === false)) {
            // Can't load
            return false;
        }
        require $classFilePath;
    }
}
