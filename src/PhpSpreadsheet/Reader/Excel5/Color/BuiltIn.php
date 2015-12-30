<?php

namespace PHPExcel\Reader\Excel5\Color;

/**
 * PHPExcel_Reader_Excel5_Color_BuiltIn
 *
 * Copyright (c) 2006 - 2015 PHPExcel
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
 * @category   PHPExcel
 * @package    \PHPExcel\Reader\Excel5
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class BuiltIn
{
    public static $map = array(
        0x00 => '000000',
        0x01 => 'FFFFFF',
        0x02 => 'FF0000',
        0x03 => '00FF00',
        0x04 => '0000FF',
        0x05 => 'FFFF00',
        0x06 => 'FF00FF',
        0x07 => '00FFFF',
        0x40 => '000000', // system window text color
        0x41 => 'FFFFFF', // system window background color
    );
}