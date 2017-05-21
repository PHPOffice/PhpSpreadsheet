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
class Protection extends Supervisor implements IComparable
{
    /** Protection styles */
    const PROTECTION_INHERIT = 'inherit';
    const PROTECTION_PROTECTED = 'protected';
    const PROTECTION_UNPROTECTED = 'unprotected';

    /**
     * Locked.
     *
     * @var string
     */
    protected $locked;

    /**
     * Hidden.
     *
     * @var string
     */
    protected $hidden;

    /**
     * Create a new Protection.
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
        if (!$isConditional) {
            $this->locked = self::PROTECTION_INHERIT;
            $this->hidden = self::PROTECTION_INHERIT;
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @return Protection
     */
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getProtection();
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
        return ['protection' => $array];
    }

    /**
     * Apply styles from array.
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getLocked()->applyFromArray(
     *        array(
     *            'locked' => TRUE,
     *            'hidden' => FALSE
     *        )
     * );
     * </code>.
     *
     * @param array $pStyles Array containing style information
     *
     * @throws PhpSpreadsheetException
     *
     * @return Protection
     */
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['locked'])) {
                $this->setLocked($pStyles['locked']);
            }
            if (isset($pStyles['hidden'])) {
                $this->setHidden($pStyles['hidden']);
            }
        }

        return $this;
    }

    /**
     * Get locked.
     *
     * @return string
     */
    public function getLocked()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getLocked();
        }

        return $this->locked;
    }

    /**
     * Set locked.
     *
     * @param string $pValue see self::PROTECTION_*
     *
     * @return Protection
     */
    public function setLocked($pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['locked' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->locked = $pValue;
        }

        return $this;
    }

    /**
     * Get hidden.
     *
     * @return string
     */
    public function getHidden()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHidden();
        }

        return $this->hidden;
    }

    /**
     * Set hidden.
     *
     * @param string $pValue see self::PROTECTION_*
     *
     * @return Protection
     */
    public function setHidden($pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['hidden' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->hidden = $pValue;
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
            $this->locked .
            $this->hidden .
            __CLASS__
        );
    }
}
