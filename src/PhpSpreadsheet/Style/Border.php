<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\IComparable;

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
class Border extends Supervisor implements IComparable
{
    /* Border style */
    const BORDER_NONE = 'none';
    const BORDER_DASHDOT = 'dashDot';
    const BORDER_DASHDOTDOT = 'dashDotDot';
    const BORDER_DASHED = 'dashed';
    const BORDER_DOTTED = 'dotted';
    const BORDER_DOUBLE = 'double';
    const BORDER_HAIR = 'hair';
    const BORDER_MEDIUM = 'medium';
    const BORDER_MEDIUMDASHDOT = 'mediumDashDot';
    const BORDER_MEDIUMDASHDOTDOT = 'mediumDashDotDot';
    const BORDER_MEDIUMDASHED = 'mediumDashed';
    const BORDER_SLANTDASHDOT = 'slantDashDot';
    const BORDER_THICK = 'thick';
    const BORDER_THIN = 'thin';

    /**
     * Border style.
     *
     * @var string
     */
    protected $borderStyle = self::BORDER_NONE;

    /**
     * Border color.
     *
     * @var Color
     */
    protected $color;

    /**
     * Parent property name.
     *
     * @var string
     */
    protected $parentPropertyName;

    /**
     * Create a new Border.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param bool $isConditional Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        // Supervisor?
        parent::__construct($isSupervisor);

        // Initialise values
        $this->color = new Color(Color::COLOR_BLACK, $isSupervisor);

        // bind parent if we are a supervisor
        if ($isSupervisor) {
            $this->color->bindParent($this, 'color');
        }
    }

    /**
     * Bind parent. Only used for supervisor.
     *
     * @param Borders $parent
     * @param string $parentPropertyName
     *
     * @return Border
     */
    public function bindParent($parent, $parentPropertyName = null)
    {
        $this->parent = $parent;
        $this->parentPropertyName = $parentPropertyName;

        return $this;
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @throws PhpSpreadsheetException
     *
     * @return Border
     */
    public function getSharedComponent()
    {
        switch ($this->parentPropertyName) {
            case 'allBorders':
            case 'horizontal':
            case 'inside':
            case 'outline':
            case 'vertical':
                throw new PhpSpreadsheetException('Cannot get shared component for a pseudo-border.');
                break;
            case 'bottom':
                return $this->parent->getSharedComponent()->getBottom();
            case 'diagonal':
                return $this->parent->getSharedComponent()->getDiagonal();
            case 'left':
                return $this->parent->getSharedComponent()->getLeft();
            case 'right':
                return $this->parent->getSharedComponent()->getRight();
            case 'top':
                return $this->parent->getSharedComponent()->getTop();
        }
    }

    /**
     * Build style array from subcomponents.
     *
     * @param array $array
     *
     * @return array
     */
    public function getStyleArray($array)
    {
        switch ($this->parentPropertyName) {
            case 'allborders':
            case 'bottom':
            case 'diagonal':
            case 'horizontal':
            case 'inside':
            case 'left':
            case 'outline':
            case 'right':
            case 'top':
            case 'vertical':
                $key = strtolower($this->parentPropertyName);
                break;
        }

        return $this->parent->getStyleArray([$key => $array]);
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getBorders()->getTop()->applyFromArray(
     *        array(
     *            'style' => Border::BORDER_DASHDOT,
     *            'color' => array(
     *                'rgb' => '808080'
     *            )
     *        )
     * );
     * </code>
     *
     * @param array $pStyles Array containing style information
     *
     * @throws PhpSpreadsheetException
     *
     * @return Border
     */
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['style'])) {
                $this->setBorderStyle($pStyles['style']);
            }
            if (isset($pStyles['color'])) {
                $this->getColor()->applyFromArray($pStyles['color']);
            }
        }

        return $this;
    }

    /**
     * Get Border style.
     *
     * @return string
     */
    public function getBorderStyle()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getBorderStyle();
        }

        return $this->borderStyle;
    }

    /**
     * Set Border style.
     *
     * @param string|bool $pValue
     *                            When passing a boolean, FALSE equates Border::BORDER_NONE
     *                                and TRUE to Border::BORDER_MEDIUM
     *
     * @return Border
     */
    public function setBorderStyle($pValue)
    {
        if (empty($pValue)) {
            $pValue = self::BORDER_NONE;
        } elseif (is_bool($pValue) && $pValue) {
            $pValue = self::BORDER_MEDIUM;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['style' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->borderStyle = $pValue;
        }

        return $this;
    }

    /**
     * Get Border Color.
     *
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set Border Color.
     *
     * @param Color $pValue
     *
     * @throws PhpSpreadsheetException
     *
     * @return Border
     */
    public function setColor(Color $pValue)
    {
        // make sure parameter is a real color and not a supervisor
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

        if ($this->isSupervisor) {
            $styleArray = $this->getColor()->getStyleArray(['argb' => $color->getARGB()]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->color = $color;
        }

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->borderStyle .
            $this->color->getHashCode() .
            __CLASS__
        );
    }
}
