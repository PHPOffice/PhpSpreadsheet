<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

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
class Protection
{
    /**
     * Sheet
     *
     * @var bool
     */
    private $sheet = false;

    /**
     * Objects
     *
     * @var bool
     */
    private $objects = false;

    /**
     * Scenarios
     *
     * @var bool
     */
    private $scenarios = false;

    /**
     * Format cells
     *
     * @var bool
     */
    private $formatCells = false;

    /**
     * Format columns
     *
     * @var bool
     */
    private $formatColumns = false;

    /**
     * Format rows
     *
     * @var bool
     */
    private $formatRows = false;

    /**
     * Insert columns
     *
     * @var bool
     */
    private $insertColumns = false;

    /**
     * Insert rows
     *
     * @var bool
     */
    private $insertRows = false;

    /**
     * Insert hyperlinks
     *
     * @var bool
     */
    private $insertHyperlinks = false;

    /**
     * Delete columns
     *
     * @var bool
     */
    private $deleteColumns = false;

    /**
     * Delete rows
     *
     * @var bool
     */
    private $deleteRows = false;

    /**
     * Select locked cells
     *
     * @var bool
     */
    private $selectLockedCells = false;

    /**
     * Sort
     *
     * @var bool
     */
    private $sort = false;

    /**
     * AutoFilter
     *
     * @var bool
     */
    private $autoFilter = false;

    /**
     * Pivot tables
     *
     * @var bool
     */
    private $pivotTables = false;

    /**
     * Select unlocked cells
     *
     * @var bool
     */
    private $selectUnlockedCells = false;

    /**
     * Password
     *
     * @var string
     */
    private $password = '';

    /**
     * Create a new Protection
     */
    public function __construct()
    {
    }

    /**
     * Is some sort of protection enabled?
     *
     * @return bool
     */
    public function isProtectionEnabled()
    {
        return $this->sheet ||
            $this->objects ||
            $this->scenarios ||
            $this->formatCells ||
            $this->formatColumns ||
            $this->formatRows ||
            $this->insertColumns ||
            $this->insertRows ||
            $this->insertHyperlinks ||
            $this->deleteColumns ||
            $this->deleteRows ||
            $this->selectLockedCells ||
            $this->sort ||
            $this->autoFilter ||
            $this->pivotTables ||
            $this->selectUnlockedCells;
    }

    /**
     * Get Sheet
     *
     * @return bool
     */
    public function getSheet()
    {
        return $this->sheet;
    }

    /**
     * Set Sheet
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setSheet($pValue = false)
    {
        $this->sheet = $pValue;

        return $this;
    }

    /**
     * Get Objects
     *
     * @return bool
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Set Objects
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setObjects($pValue = false)
    {
        $this->objects = $pValue;

        return $this;
    }

    /**
     * Get Scenarios
     *
     * @return bool
     */
    public function getScenarios()
    {
        return $this->scenarios;
    }

    /**
     * Set Scenarios
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setScenarios($pValue = false)
    {
        $this->scenarios = $pValue;

        return $this;
    }

    /**
     * Get FormatCells
     *
     * @return bool
     */
    public function getFormatCells()
    {
        return $this->formatCells;
    }

    /**
     * Set FormatCells
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setFormatCells($pValue = false)
    {
        $this->formatCells = $pValue;

        return $this;
    }

    /**
     * Get FormatColumns
     *
     * @return bool
     */
    public function getFormatColumns()
    {
        return $this->formatColumns;
    }

    /**
     * Set FormatColumns
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setFormatColumns($pValue = false)
    {
        $this->formatColumns = $pValue;

        return $this;
    }

    /**
     * Get FormatRows
     *
     * @return bool
     */
    public function getFormatRows()
    {
        return $this->formatRows;
    }

    /**
     * Set FormatRows
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setFormatRows($pValue = false)
    {
        $this->formatRows = $pValue;

        return $this;
    }

    /**
     * Get InsertColumns
     *
     * @return bool
     */
    public function getInsertColumns()
    {
        return $this->insertColumns;
    }

    /**
     * Set InsertColumns
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setInsertColumns($pValue = false)
    {
        $this->insertColumns = $pValue;

        return $this;
    }

    /**
     * Get InsertRows
     *
     * @return bool
     */
    public function getInsertRows()
    {
        return $this->insertRows;
    }

    /**
     * Set InsertRows
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setInsertRows($pValue = false)
    {
        $this->insertRows = $pValue;

        return $this;
    }

    /**
     * Get InsertHyperlinks
     *
     * @return bool
     */
    public function getInsertHyperlinks()
    {
        return $this->insertHyperlinks;
    }

    /**
     * Set InsertHyperlinks
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setInsertHyperlinks($pValue = false)
    {
        $this->insertHyperlinks = $pValue;

        return $this;
    }

    /**
     * Get DeleteColumns
     *
     * @return bool
     */
    public function getDeleteColumns()
    {
        return $this->deleteColumns;
    }

    /**
     * Set DeleteColumns
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setDeleteColumns($pValue = false)
    {
        $this->deleteColumns = $pValue;

        return $this;
    }

    /**
     * Get DeleteRows
     *
     * @return bool
     */
    public function getDeleteRows()
    {
        return $this->deleteRows;
    }

    /**
     * Set DeleteRows
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setDeleteRows($pValue = false)
    {
        $this->deleteRows = $pValue;

        return $this;
    }

    /**
     * Get SelectLockedCells
     *
     * @return bool
     */
    public function getSelectLockedCells()
    {
        return $this->selectLockedCells;
    }

    /**
     * Set SelectLockedCells
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setSelectLockedCells($pValue = false)
    {
        $this->selectLockedCells = $pValue;

        return $this;
    }

    /**
     * Get Sort
     *
     * @return bool
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set Sort
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setSort($pValue = false)
    {
        $this->sort = $pValue;

        return $this;
    }

    /**
     * Get AutoFilter
     *
     * @return bool
     */
    public function getAutoFilter()
    {
        return $this->autoFilter;
    }

    /**
     * Set AutoFilter
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setAutoFilter($pValue = false)
    {
        $this->autoFilter = $pValue;

        return $this;
    }

    /**
     * Get PivotTables
     *
     * @return bool
     */
    public function getPivotTables()
    {
        return $this->pivotTables;
    }

    /**
     * Set PivotTables
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setPivotTables($pValue = false)
    {
        $this->pivotTables = $pValue;

        return $this;
    }

    /**
     * Get SelectUnlockedCells
     *
     * @return bool
     */
    public function getSelectUnlockedCells()
    {
        return $this->selectUnlockedCells;
    }

    /**
     * Set SelectUnlockedCells
     *
     * @param bool $pValue
     * @return Protection
     */
    public function setSelectUnlockedCells($pValue = false)
    {
        $this->selectUnlockedCells = $pValue;

        return $this;
    }

    /**
     * Get Password (hashed)
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set Password
     *
     * @param string     $pValue
     * @param bool     $pAlreadyHashed If the password has already been hashed, set this to true
     * @return Protection
     */
    public function setPassword($pValue = '', $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = \PhpOffice\PhpSpreadsheet\Shared\PasswordHasher::hashPassword($pValue);
        }
        $this->password = $pValue;

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
