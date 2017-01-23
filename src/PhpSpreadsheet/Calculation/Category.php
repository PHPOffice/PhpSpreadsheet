<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

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
abstract class Category
{
    /* Function categories */
    const CATEGORY_CUBE = 'Cube';
    const CATEGORY_DATABASE = 'Database';
    const CATEGORY_DATE_AND_TIME = 'Date and Time';
    const CATEGORY_ENGINEERING = 'Engineering';
    const CATEGORY_FINANCIAL = 'Financial';
    const CATEGORY_INFORMATION = 'Information';
    const CATEGORY_LOGICAL = 'Logical';
    const CATEGORY_LOOKUP_AND_REFERENCE = 'Lookup and Reference';
    const CATEGORY_MATH_AND_TRIG = 'Math and Trig';
    const CATEGORY_STATISTICAL = 'Statistical';
    const CATEGORY_TEXT_AND_DATA = 'Text and Data';
}
