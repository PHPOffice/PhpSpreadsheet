<?php

namespace PhpOffice\PhpSpreadsheet;

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
class NamedRange
{
    /**
     * Range name.
     *
     * @var string
     */
    private $name;

    /**
     * Worksheet on which the named range can be resolved.
     *
     * @var Worksheet
     */
    private $worksheet;

    /**
     * Range of the referenced cells.
     *
     * @var string
     */
    private $range;

    /**
     * Is the named range local? (i.e. can only be used on $this->worksheet).
     *
     * @var bool
     */
    private $localOnly;

    /**
     * Scope.
     *
     * @var Worksheet
     */
    private $scope;

    /**
     * Create a new NamedRange.
     *
     * @param string $pName
     * @param Worksheet $pWorksheet
     * @param string $pRange
     * @param bool $pLocalOnly
     * @param Worksheet|null $pScope Scope. Only applies when $pLocalOnly = true. Null for global scope.
     *
     * @throws Exception
     */
    public function __construct($pName, Worksheet $pWorksheet, $pRange = 'A1', $pLocalOnly = false, $pScope = null)
    {
        // Validate data
        if (($pName === null) || ($pWorksheet === null) || ($pRange === null)) {
            throw new Exception('Parameters can not be null.');
        }

        // Set local members
        $this->name = $pName;
        $this->worksheet = $pWorksheet;
        $this->range = $pRange;
        $this->localOnly = $pLocalOnly;
        $this->scope = ($pLocalOnly == true) ? (($pScope == null) ? $pWorksheet : $pScope) : null;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $value
     *
     * @return NamedRange
     */
    public function setName($value = null)
    {
        if ($value !== null) {
            // Old title
            $oldTitle = $this->name;

            // Re-attach
            if ($this->worksheet !== null) {
                $this->worksheet->getParent()->removeNamedRange($this->name, $this->worksheet);
            }
            $this->name = $value;

            if ($this->worksheet !== null) {
                $this->worksheet->getParent()->addNamedRange($this);
            }

            // New title
            $newTitle = $this->name;
            ReferenceHelper::getInstance()->updateNamedFormulas($this->worksheet->getParent(), $oldTitle, $newTitle);
        }

        return $this;
    }

    /**
     * Get worksheet.
     *
     * @return Worksheet
     */
    public function getWorksheet()
    {
        return $this->worksheet;
    }

    /**
     * Set worksheet.
     *
     * @param Worksheet $value
     *
     * @return NamedRange
     */
    public function setWorksheet(Worksheet $value = null)
    {
        if ($value !== null) {
            $this->worksheet = $value;
        }

        return $this;
    }

    /**
     * Get range.
     *
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Set range.
     *
     * @param string $value
     *
     * @return NamedRange
     */
    public function setRange($value = null)
    {
        if ($value !== null) {
            $this->range = $value;
        }

        return $this;
    }

    /**
     * Get localOnly.
     *
     * @return bool
     */
    public function getLocalOnly()
    {
        return $this->localOnly;
    }

    /**
     * Set localOnly.
     *
     * @param bool $value
     *
     * @return NamedRange
     */
    public function setLocalOnly($value = false)
    {
        $this->localOnly = $value;
        $this->scope = $value ? $this->worksheet : null;

        return $this;
    }

    /**
     * Get scope.
     *
     * @return Worksheet|null
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set scope.
     *
     * @param Worksheet|null $value
     *
     * @return NamedRange
     */
    public function setScope(Worksheet $value = null)
    {
        $this->scope = $value;
        $this->localOnly = ($value == null) ? false : true;

        return $this;
    }

    /**
     * Resolve a named range to a regular cell range.
     *
     * @param string $pNamedRange Named range
     * @param Worksheet|null $pSheet Scope. Use null for global scope
     *
     * @return NamedRange
     */
    public static function resolveRange($pNamedRange, Worksheet $pSheet = null)
    {
        return $pSheet->getParent()->getNamedRange($pNamedRange, $pSheet);
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
