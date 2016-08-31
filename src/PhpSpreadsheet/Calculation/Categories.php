<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

/**
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
class Categories
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

    /**
     * Category (represented by CATEGORY_*)
     *
     * @var string
     */
    private $category;

    /**
     * Excel function name
     *
     * @var string
     */
    private $excelName;

    /**
     * Spreadsheet function name
     *
     * @var string
     */
    private $spreadsheetName;

    /**
     * Create a new Categories
     * @param     string        $pCategory         Category (represented by CATEGORY_*)
     * @param     string        $pExcelName        Excel function name
     * @param     string        $spreadsheetName     Spreadsheet internal function name
     * @throws    Exception
     */
    public function __construct($pCategory = null, $pExcelName = null, $spreadsheetName = null)
    {
        if (($pCategory !== null) && ($pExcelName !== null) && ($spreadsheetName !== null)) {
            // Initialise values
            $this->category = $pCategory;
            $this->excelName = $pExcelName;
            $this->spreadsheetName = $spreadsheetName;
        } else {
            throw new Exception('Invalid parameters passed.');
        }
    }

    /**
     * Get Category (represented by CATEGORY_*)
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set Category (represented by CATEGORY_*)
     *
     * @param   string        $value
     * @throws  Exception
     */
    public function setCategory($value = null)
    {
        if (!is_null($value)) {
            $this->category = $value;
        } else {
            throw new Exception('Invalid parameter passed.');
        }
    }

    /**
     * Get Excel function name
     *
     * @return string
     */
    public function getExcelName()
    {
        return $this->excelName;
    }

    /**
     * Set Excel function name
     *
     * @param string    $value
     */
    public function setExcelName($value)
    {
        $this->excelName = $value;
    }

    /**
     * Get Spreadsheet function name
     *
     * @return string
     */
    public function getSpreadsheetName()
    {
        return $this->spreadsheetName;
    }

    /**
     * Set Spreadsheet function  name
     *
     * @param string    $value
     */
    public function setSpreadsheetName($value)
    {
        $this->spreadsheetName = $value;
    }
}
