<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Style\CellAlignment;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Style\CellBorder;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Style\CellFill;

// Original file header of PEAR::Spreadsheet_Excel_Writer_Format (used as the base for this class):
// -----------------------------------------------------------------------------------------
// /*
// *  Module written/ported by Xavier Noguer <xnoguer@rezebra.com>
// *
// *  The majority of this is _NOT_ my code.  I simply ported it from the
// *  PERL Spreadsheet::WriteExcel module.
// *
// *  The author of the Spreadsheet::WriteExcel module is John McNamara
// *  <jmcnamara@cpan.org>
// *
// *  I _DO_ maintain this code, and John McNamara has nothing to do with the
// *  porting of this code to PHP.  Any questions directly related to this
// *  class library should be directed to me.
// *
// *  License Information:
// *
// *    Spreadsheet_Excel_Writer:  A library for generating Excel Spreadsheets
// *    Copyright (c) 2002-2003 Xavier Noguer xnoguer@rezebra.com
// *
// *    This library is free software; you can redistribute it and/or
// *    modify it under the terms of the GNU Lesser General Public
// *    License as published by the Free Software Foundation; either
// *    version 2.1 of the License, or (at your option) any later version.
// *
// *    This library is distributed in the hope that it will be useful,
// *    but WITHOUT ANY WARRANTY; without even the implied warranty of
// *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// *    Lesser General Public License for more details.
// *
// *    You should have received a copy of the GNU Lesser General Public
// *    License along with this library; if not, write to the Free Software
// *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// */
class Xf
{
    /**
     * Style XF or a cell XF ?
     */
    private bool $isStyleXf;

    /**
     * Index to the FONT record. Index 4 does not exist.
     */
    private int $fontIndex;

    /**
     * An index (2 bytes) to a FORMAT record (number format).
     */
    private int $numberFormatIndex;

    /**
     * 1 bit, apparently not used.
     */
    private int $textJustLast;

    /**
     * The cell's foreground color.
     */
    private int $foregroundColor;

    /**
     * The cell's background color.
     */
    private int $backgroundColor;

    /**
     * Color of the bottom border of the cell.
     */
    private int $bottomBorderColor;

    /**
     * Color of the top border of the cell.
     */
    private int $topBorderColor;

    /**
     * Color of the left border of the cell.
     */
    private int $leftBorderColor;

    /**
     * Color of the right border of the cell.
     */
    private int $rightBorderColor;

    //private $diag; // theoretically int, not yet implemented
    private int $diagColor;

    private Style $style;

    /**
     * Constructor.
     *
     * @param Style $style The XF format
     */
    public function __construct(Style $style)
    {
        $this->isStyleXf = false;
        $this->fontIndex = 0;

        $this->numberFormatIndex = 0;

        $this->textJustLast = 0;

        $this->foregroundColor = 0x40;
        $this->backgroundColor = 0x41;

        //$this->diag = 0;

        $this->bottomBorderColor = 0x40;
        $this->topBorderColor = 0x40;
        $this->leftBorderColor = 0x40;
        $this->rightBorderColor = 0x40;
        $this->diagColor = 0x40;
        $this->style = $style;
    }

    /**
     * Generate an Excel BIFF XF record (style or cell).
     *
     * @return string The XF record
     */
    public function writeXf(): string
    {
        // Set the type of the XF record and some of the attributes.
        if ($this->isStyleXf) {
            $style = 0xFFF5;
        } else {
            $style = self::mapLocked($this->style->getProtection()->getLocked());
            $style |= self::mapHidden($this->style->getProtection()->getHidden()) << 1;
        }

        // Flags to indicate if attributes have been set.
        $atr_num = ($this->numberFormatIndex != 0) ? 1 : 0;
        $atr_fnt = ($this->fontIndex != 0) ? 1 : 0;
        $atr_alc = ((int) $this->style->getAlignment()->getWrapText()) ? 1 : 0;
        $atr_bdr = (CellBorder::style($this->style->getBorders()->getBottom())
            || CellBorder::style($this->style->getBorders()->getTop())
            || CellBorder::style($this->style->getBorders()->getLeft())
            || CellBorder::style($this->style->getBorders()->getRight())) ? 1 : 0;
        $atr_pat = ($this->foregroundColor != 0x40) ? 1 : 0;
        $atr_pat = ($this->backgroundColor != 0x41) ? 1 : $atr_pat;
        $atr_pat = CellFill::style($this->style->getFill()) ? 1 : $atr_pat;
        $atr_prot = self::mapLocked($this->style->getProtection()->getLocked())
            | self::mapHidden($this->style->getProtection()->getHidden());

        // Zero the default border colour if the border has not been set.
        if (CellBorder::style($this->style->getBorders()->getBottom()) == 0) {
            $this->bottomBorderColor = 0;
        }
        if (CellBorder::style($this->style->getBorders()->getTop()) == 0) {
            $this->topBorderColor = 0;
        }
        if (CellBorder::style($this->style->getBorders()->getRight()) == 0) {
            $this->rightBorderColor = 0;
        }
        if (CellBorder::style($this->style->getBorders()->getLeft()) == 0) {
            $this->leftBorderColor = 0;
        }
        if (CellBorder::style($this->style->getBorders()->getDiagonal()) == 0) {
            $this->diagColor = 0;
        }

        $record = 0x00E0; // Record identifier
        $length = 0x0014; // Number of bytes to follow

        $ifnt = $this->fontIndex; // Index to FONT record
        $ifmt = $this->numberFormatIndex; // Index to FORMAT record

        // Alignment
        $align = CellAlignment::horizontal($this->style->getAlignment());
        $align |= CellAlignment::wrap($this->style->getAlignment()) << 3;
        $align |= CellAlignment::vertical($this->style->getAlignment()) << 4;
        $align |= $this->textJustLast << 7;

        $used_attrib = $atr_num << 2;
        $used_attrib |= $atr_fnt << 3;
        $used_attrib |= $atr_alc << 4;
        $used_attrib |= $atr_bdr << 5;
        $used_attrib |= $atr_pat << 6;
        $used_attrib |= $atr_prot << 7;

        $icv = $this->foregroundColor; // fg and bg pattern colors
        $icv |= $this->backgroundColor << 7;

        $border1 = CellBorder::style($this->style->getBorders()->getLeft()); // Border line style and color
        $border1 |= CellBorder::style($this->style->getBorders()->getRight()) << 4;
        $border1 |= CellBorder::style($this->style->getBorders()->getTop()) << 8;
        $border1 |= CellBorder::style($this->style->getBorders()->getBottom()) << 12;
        $border1 |= $this->leftBorderColor << 16;
        $border1 |= $this->rightBorderColor << 23;

        $diagonalDirection = $this->style->getBorders()->getDiagonalDirection();
        $diag_tl_to_rb = $diagonalDirection == Borders::DIAGONAL_BOTH
            || $diagonalDirection == Borders::DIAGONAL_DOWN;
        $diag_tr_to_lb = $diagonalDirection == Borders::DIAGONAL_BOTH
            || $diagonalDirection == Borders::DIAGONAL_UP;
        $border1 |= $diag_tl_to_rb << 30;
        $border1 |= $diag_tr_to_lb << 31;

        $border2 = $this->topBorderColor; // Border color
        $border2 |= $this->bottomBorderColor << 7;
        $border2 |= $this->diagColor << 14;
        $border2 |= CellBorder::style($this->style->getBorders()->getDiagonal()) << 21;
        $border2 |= CellFill::style($this->style->getFill()) << 26;

        $header = pack('vv', $record, $length);

        //BIFF8 options: identation, shrinkToFit and  text direction
        $biff8_options = $this->style->getAlignment()->getIndent();
        $biff8_options |= (int) $this->style->getAlignment()->getShrinkToFit() << 4;

        $data = pack('vvvC', $ifnt, $ifmt, $style, $align);
        $data .= pack('CCC', self::mapTextRotation((int) $this->style->getAlignment()->getTextRotation()), $biff8_options, $used_attrib);
        $data .= pack('VVv', $border1, $border2, $icv);

        return $header . $data;
    }

    /**
     * Is this a style XF ?
     */
    public function setIsStyleXf(bool $value): void
    {
        $this->isStyleXf = $value;
    }

    /**
     * Sets the cell's bottom border color.
     *
     * @param int $colorIndex Color index
     */
    public function setBottomColor(int $colorIndex): void
    {
        $this->bottomBorderColor = $colorIndex;
    }

    /**
     * Sets the cell's top border color.
     *
     * @param int $colorIndex Color index
     */
    public function setTopColor(int $colorIndex): void
    {
        $this->topBorderColor = $colorIndex;
    }

    /**
     * Sets the cell's left border color.
     *
     * @param int $colorIndex Color index
     */
    public function setLeftColor(int $colorIndex): void
    {
        $this->leftBorderColor = $colorIndex;
    }

    /**
     * Sets the cell's right border color.
     *
     * @param int $colorIndex Color index
     */
    public function setRightColor(int $colorIndex): void
    {
        $this->rightBorderColor = $colorIndex;
    }

    /**
     * Sets the cell's diagonal border color.
     *
     * @param int $colorIndex Color index
     */
    public function setDiagColor(int $colorIndex): void
    {
        $this->diagColor = $colorIndex;
    }

    /**
     * Sets the cell's foreground color.
     *
     * @param int $colorIndex Color index
     */
    public function setFgColor(int $colorIndex): void
    {
        $this->foregroundColor = $colorIndex;
    }

    /**
     * Sets the cell's background color.
     *
     * @param int $colorIndex Color index
     */
    public function setBgColor(int $colorIndex): void
    {
        $this->backgroundColor = $colorIndex;
    }

    /**
     * Sets the index to the number format record
     * It can be date, time, currency, etc...
     *
     * @param int $numberFormatIndex Index to format record
     */
    public function setNumberFormatIndex(int $numberFormatIndex): void
    {
        $this->numberFormatIndex = $numberFormatIndex;
    }

    /**
     * Set the font index.
     *
     * @param int $value Font index, note that value 4 does not exist
     */
    public function setFontIndex(int $value): void
    {
        $this->fontIndex = $value;
    }

    /**
     * Map to BIFF8 codes for text rotation angle.
     */
    private static function mapTextRotation(int $textRotation): int
    {
        if ($textRotation >= 0) {
            return $textRotation;
        }
        if ($textRotation == Alignment::TEXTROTATION_STACK_PHPSPREADSHEET) {
            return Alignment::TEXTROTATION_STACK_EXCEL;
        }

        return 90 - $textRotation;
    }

    private const LOCK_ARRAY = [
        Protection::PROTECTION_INHERIT => 1,
        Protection::PROTECTION_PROTECTED => 1,
        Protection::PROTECTION_UNPROTECTED => 0,
    ];

    /**
     * Map locked values.
     */
    private static function mapLocked(?string $locked): int
    {
        return $locked !== null && array_key_exists($locked, self::LOCK_ARRAY) ? self::LOCK_ARRAY[$locked] : 1;
    }

    private const HIDDEN_ARRAY = [
        Protection::PROTECTION_INHERIT => 0,
        Protection::PROTECTION_PROTECTED => 1,
        Protection::PROTECTION_UNPROTECTED => 0,
    ];

    /**
     * Map hidden.
     */
    private static function mapHidden(?string $hidden): int
    {
        return $hidden !== null && array_key_exists($hidden, self::HIDDEN_ARRAY) ? self::HIDDEN_ARRAY[$hidden] : 0;
    }
}
