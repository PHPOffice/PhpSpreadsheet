<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

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
class PageMargins
{
    /**
     * Left.
     *
     * @var float
     */
    private $left = 0.7;

    /**
     * Right.
     *
     * @var float
     */
    private $right = 0.7;

    /**
     * Top.
     *
     * @var float
     */
    private $top = 0.75;

    /**
     * Bottom.
     *
     * @var float
     */
    private $bottom = 0.75;

    /**
     * Header.
     *
     * @var float
     */
    private $header = 0.3;

    /**
     * Footer.
     *
     * @var float
     */
    private $footer = 0.3;

    /**
     * Create a new PageMargins.
     */
    public function __construct()
    {
    }

    /**
     * Get Left.
     *
     * @return float
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set Left.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setLeft($pValue)
    {
        $this->left = $pValue;

        return $this;
    }

    /**
     * Get Right.
     *
     * @return float
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set Right.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setRight($pValue)
    {
        $this->right = $pValue;

        return $this;
    }

    /**
     * Get Top.
     *
     * @return float
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * Set Top.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setTop($pValue)
    {
        $this->top = $pValue;

        return $this;
    }

    /**
     * Get Bottom.
     *
     * @return float
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * Set Bottom.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setBottom($pValue)
    {
        $this->bottom = $pValue;

        return $this;
    }

    /**
     * Get Header.
     *
     * @return float
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set Header.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setHeader($pValue)
    {
        $this->header = $pValue;

        return $this;
    }

    /**
     * Get Footer.
     *
     * @return float
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set Footer.
     *
     * @param float $pValue
     *
     * @return PageMargins
     */
    public function setFooter($pValue)
    {
        $this->footer = $pValue;

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
