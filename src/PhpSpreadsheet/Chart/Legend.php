<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

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
 * @category    PhpSpreadsheet
 *
 * @copyright   Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Legend
{
    /** Legend positions */
    const XL_LEGEND_POSITION_BOTTOM = -4107; //    Below the chart.
    const XL_LEGEND_POSITION_CORNER = 2; //    In the upper right-hand corner of the chart border.
    const XL_LEGEND_POSITION_CUSTOM = -4161; //    A custom position.
    const XL_LEGEND_POSITION_LEFT = -4131; //    Left of the chart.
    const XL_LEGEND_POSITION_RIGHT = -4152; //    Right of the chart.
    const XL_LEGEND_POSITION_TOP = -4160; //    Above the chart.

    const POSITION_RIGHT = 'r';
    const POSITION_LEFT = 'l';
    const POSITION_BOTTOM = 'b';
    const POSITION_TOP = 't';
    const POSITION_TOPRIGHT = 'tr';

    private static $positionXLref = [
        self::XL_LEGEND_POSITION_BOTTOM => self::POSITION_BOTTOM,
        self::XL_LEGEND_POSITION_CORNER => self::POSITION_TOPRIGHT,
        self::XL_LEGEND_POSITION_CUSTOM => '??',
        self::XL_LEGEND_POSITION_LEFT => self::POSITION_LEFT,
        self::XL_LEGEND_POSITION_RIGHT => self::POSITION_RIGHT,
        self::XL_LEGEND_POSITION_TOP => self::POSITION_TOP,
    ];

    /**
     * Legend position.
     *
     * @var string
     */
    private $position = self::POSITION_RIGHT;

    /**
     * Allow overlay of other elements?
     *
     * @var bool
     */
    private $overlay = true;

    /**
     * Legend Layout.
     *
     * @var Layout
     */
    private $layout = null;

    /**
     * Create a new Legend.
     *
     * @param string $position
     * @param Layout|null $layout
     * @param bool $overlay
     */
    public function __construct($position = self::POSITION_RIGHT, Layout $layout = null, $overlay = false)
    {
        $this->setPosition($position);
        $this->layout = $layout;
        $this->setOverlay($overlay);
    }

    /**
     * Get legend position as an excel string value.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get legend position using an excel string value.
     *
     * @param string $position see self::POSITION_*
     */
    public function setPosition($position)
    {
        if (!in_array($position, self::$positionXLref)) {
            return false;
        }

        $this->position = $position;

        return true;
    }

    /**
     * Get legend position as an Excel internal numeric value.
     *
     * @return int
     */
    public function getPositionXL()
    {
        return array_search($this->position, self::$positionXLref);
    }

    /**
     * Set legend position using an Excel internal numeric value.
     *
     * @param int $positionXL see self::XL_LEGEND_POSITION_*
     */
    public function setPositionXL($positionXL)
    {
        if (!isset(self::$positionXLref[$positionXL])) {
            return false;
        }

        $this->position = self::$positionXLref[$positionXL];

        return true;
    }

    /**
     * Get allow overlay of other elements?
     *
     * @return bool
     */
    public function getOverlay()
    {
        return $this->overlay;
    }

    /**
     * Set allow overlay of other elements?
     *
     * @param bool $overlay
     *
     * @return bool
     */
    public function setOverlay($overlay)
    {
        if (!is_bool($overlay)) {
            return false;
        }

        $this->overlay = $overlay;

        return true;
    }

    /**
     * Get Layout.
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
