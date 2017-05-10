<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;

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
class Style extends WriterPart
{
    /**
     * Write styles to XML format.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return string XML Output
     */
    public function writeStyles(Spreadsheet $spreadsheet)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // styleSheet
        $objWriter->startElement('styleSheet');
        $objWriter->writeAttribute('xml:space', 'preserve');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        // numFmts
        $objWriter->startElement('numFmts');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getNumFmtHashTable()->count());

        // numFmt
        for ($i = 0; $i < $this->getParentWriter()->getNumFmtHashTable()->count(); ++$i) {
            $this->writeNumFmt($objWriter, $this->getParentWriter()->getNumFmtHashTable()->getByIndex($i), $i);
        }

        $objWriter->endElement();

        // fonts
        $objWriter->startElement('fonts');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getFontHashTable()->count());

        // font
        for ($i = 0; $i < $this->getParentWriter()->getFontHashTable()->count(); ++$i) {
            $this->writeFont($objWriter, $this->getParentWriter()->getFontHashTable()->getByIndex($i));
        }

        $objWriter->endElement();

        // fills
        $objWriter->startElement('fills');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getFillHashTable()->count());

        // fill
        for ($i = 0; $i < $this->getParentWriter()->getFillHashTable()->count(); ++$i) {
            $this->writeFill($objWriter, $this->getParentWriter()->getFillHashTable()->getByIndex($i));
        }

        $objWriter->endElement();

        // borders
        $objWriter->startElement('borders');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getBordersHashTable()->count());

        // border
        for ($i = 0; $i < $this->getParentWriter()->getBordersHashTable()->count(); ++$i) {
            $this->writeBorder($objWriter, $this->getParentWriter()->getBordersHashTable()->getByIndex($i));
        }

        $objWriter->endElement();

        // cellStyleXfs
        $objWriter->startElement('cellStyleXfs');
        $objWriter->writeAttribute('count', 1);

        // xf
        $objWriter->startElement('xf');
        $objWriter->writeAttribute('numFmtId', 0);
        $objWriter->writeAttribute('fontId', 0);
        $objWriter->writeAttribute('fillId', 0);
        $objWriter->writeAttribute('borderId', 0);
        $objWriter->endElement();

        $objWriter->endElement();

        // cellXfs
        $objWriter->startElement('cellXfs');
        $objWriter->writeAttribute('count', count($spreadsheet->getCellXfCollection()));

        // xf
        foreach ($spreadsheet->getCellXfCollection() as $cellXf) {
            $this->writeCellStyleXf($objWriter, $cellXf, $spreadsheet);
        }

        $objWriter->endElement();

        // cellStyles
        $objWriter->startElement('cellStyles');
        $objWriter->writeAttribute('count', 1);

        // cellStyle
        $objWriter->startElement('cellStyle');
        $objWriter->writeAttribute('name', 'Normal');
        $objWriter->writeAttribute('xfId', 0);
        $objWriter->writeAttribute('builtinId', 0);
        $objWriter->endElement();

        $objWriter->endElement();

        // dxfs
        $objWriter->startElement('dxfs');
        $objWriter->writeAttribute('count', $this->getParentWriter()->getStylesConditionalHashTable()->count());

        // dxf
        for ($i = 0; $i < $this->getParentWriter()->getStylesConditionalHashTable()->count(); ++$i) {
            $this->writeCellStyleDxf($objWriter, $this->getParentWriter()->getStylesConditionalHashTable()->getByIndex($i)->getStyle());
        }

        $objWriter->endElement();

        // tableStyles
        $objWriter->startElement('tableStyles');
        $objWriter->writeAttribute('defaultTableStyle', 'TableStyleMedium9');
        $objWriter->writeAttribute('defaultPivotStyle', 'PivotTableStyle1');
        $objWriter->endElement();

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write Fill.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Fill $pFill Fill style
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeFill(XMLWriter $objWriter, Fill $pFill)
    {
        // Check if this is a pattern type or gradient type
        if ($pFill->getFillType() === Fill::FILL_GRADIENT_LINEAR ||
            $pFill->getFillType() === Fill::FILL_GRADIENT_PATH) {
            // Gradient fill
            $this->writeGradientFill($objWriter, $pFill);
        } elseif ($pFill->getFillType() !== null) {
            // Pattern fill
            $this->writePatternFill($objWriter, $pFill);
        }
    }

    /**
     * Write Gradient Fill.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Fill $pFill Fill style
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeGradientFill(XMLWriter $objWriter, Fill $pFill)
    {
        // fill
        $objWriter->startElement('fill');

        // gradientFill
        $objWriter->startElement('gradientFill');
        $objWriter->writeAttribute('type', $pFill->getFillType());
        $objWriter->writeAttribute('degree', $pFill->getRotation());

        // stop
        $objWriter->startElement('stop');
        $objWriter->writeAttribute('position', '0');

        // color
        $objWriter->startElement('color');
        $objWriter->writeAttribute('rgb', $pFill->getStartColor()->getARGB());
        $objWriter->endElement();

        $objWriter->endElement();

        // stop
        $objWriter->startElement('stop');
        $objWriter->writeAttribute('position', '1');

        // color
        $objWriter->startElement('color');
        $objWriter->writeAttribute('rgb', $pFill->getEndColor()->getARGB());
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Pattern Fill.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Fill $pFill Fill style
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writePatternFill(XMLWriter $objWriter, Fill $pFill)
    {
        // fill
        $objWriter->startElement('fill');

        // patternFill
        $objWriter->startElement('patternFill');
        $objWriter->writeAttribute('patternType', $pFill->getFillType());

        if ($pFill->getFillType() !== Fill::FILL_NONE) {
            // fgColor
            if ($pFill->getStartColor()->getARGB()) {
                $objWriter->startElement('fgColor');
                $objWriter->writeAttribute('rgb', $pFill->getStartColor()->getARGB());
                $objWriter->endElement();
            }
        }
        if ($pFill->getFillType() !== Fill::FILL_NONE) {
            // bgColor
            if ($pFill->getEndColor()->getARGB()) {
                $objWriter->startElement('bgColor');
                $objWriter->writeAttribute('rgb', $pFill->getEndColor()->getARGB());
                $objWriter->endElement();
            }
        }

        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Font.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Font $pFont Font style
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeFont(XMLWriter $objWriter, Font $pFont)
    {
        // font
        $objWriter->startElement('font');
        //    Weird! The order of these elements actually makes a difference when opening Xlsx
        //        files in Excel2003 with the compatibility pack. It's not documented behaviour,
        //        and makes for a real WTF!

        // Bold. We explicitly write this element also when false (like MS Office Excel 2007 does
        // for conditional formatting). Otherwise it will apparently not be picked up in conditional
        // formatting style dialog
        if ($pFont->getBold() !== null) {
            $objWriter->startElement('b');
            $objWriter->writeAttribute('val', $pFont->getBold() ? '1' : '0');
            $objWriter->endElement();
        }

        // Italic
        if ($pFont->getItalic() !== null) {
            $objWriter->startElement('i');
            $objWriter->writeAttribute('val', $pFont->getItalic() ? '1' : '0');
            $objWriter->endElement();
        }

        // Strikethrough
        if ($pFont->getStrikethrough() !== null) {
            $objWriter->startElement('strike');
            $objWriter->writeAttribute('val', $pFont->getStrikethrough() ? '1' : '0');
            $objWriter->endElement();
        }

        // Underline
        if ($pFont->getUnderline() !== null) {
            $objWriter->startElement('u');
            $objWriter->writeAttribute('val', $pFont->getUnderline());
            $objWriter->endElement();
        }

        // Superscript / subscript
        if ($pFont->getSuperScript() === true || $pFont->getSubScript() === true) {
            $objWriter->startElement('vertAlign');
            if ($pFont->getSuperScript() === true) {
                $objWriter->writeAttribute('val', 'superscript');
            } elseif ($pFont->getSubScript() === true) {
                $objWriter->writeAttribute('val', 'subscript');
            }
            $objWriter->endElement();
        }

        // Size
        if ($pFont->getSize() !== null) {
            $objWriter->startElement('sz');
            $objWriter->writeAttribute('val', StringHelper::formatNumber($pFont->getSize()));
            $objWriter->endElement();
        }

        // Foreground color
        if ($pFont->getColor()->getARGB() !== null) {
            $objWriter->startElement('color');
            $objWriter->writeAttribute('rgb', $pFont->getColor()->getARGB());
            $objWriter->endElement();
        }

        // Name
        if ($pFont->getName() !== null) {
            $objWriter->startElement('name');
            $objWriter->writeAttribute('val', $pFont->getName());
            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    /**
     * Write Border.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Borders $pBorders Borders style
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeBorder(XMLWriter $objWriter, Borders $pBorders)
    {
        // Write border
        $objWriter->startElement('border');
        // Diagonal?
        switch ($pBorders->getDiagonalDirection()) {
            case Borders::DIAGONAL_UP:
                $objWriter->writeAttribute('diagonalUp', 'true');
                $objWriter->writeAttribute('diagonalDown', 'false');
                break;
            case Borders::DIAGONAL_DOWN:
                $objWriter->writeAttribute('diagonalUp', 'false');
                $objWriter->writeAttribute('diagonalDown', 'true');
                break;
            case Borders::DIAGONAL_BOTH:
                $objWriter->writeAttribute('diagonalUp', 'true');
                $objWriter->writeAttribute('diagonalDown', 'true');
                break;
        }

        // BorderPr
        $this->writeBorderPr($objWriter, 'left', $pBorders->getLeft());
        $this->writeBorderPr($objWriter, 'right', $pBorders->getRight());
        $this->writeBorderPr($objWriter, 'top', $pBorders->getTop());
        $this->writeBorderPr($objWriter, 'bottom', $pBorders->getBottom());
        $this->writeBorderPr($objWriter, 'diagonal', $pBorders->getDiagonal());
        $objWriter->endElement();
    }

    /**
     * Write Cell Style Xf.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Style $pStyle Style
     * @param Spreadsheet $spreadsheet Workbook
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeCellStyleXf(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Style $pStyle, Spreadsheet $spreadsheet)
    {
        // xf
        $objWriter->startElement('xf');
        $objWriter->writeAttribute('xfId', 0);
        $objWriter->writeAttribute('fontId', (int) $this->getParentWriter()->getFontHashTable()->getIndexForHashCode($pStyle->getFont()->getHashCode()));
        if ($pStyle->getQuotePrefix()) {
            $objWriter->writeAttribute('quotePrefix', 1);
        }

        if ($pStyle->getNumberFormat()->getBuiltInFormatCode() === false) {
            $objWriter->writeAttribute('numFmtId', (int) ($this->getParentWriter()->getNumFmtHashTable()->getIndexForHashCode($pStyle->getNumberFormat()->getHashCode()) + 164));
        } else {
            $objWriter->writeAttribute('numFmtId', (int) $pStyle->getNumberFormat()->getBuiltInFormatCode());
        }

        $objWriter->writeAttribute('fillId', (int) $this->getParentWriter()->getFillHashTable()->getIndexForHashCode($pStyle->getFill()->getHashCode()));
        $objWriter->writeAttribute('borderId', (int) $this->getParentWriter()->getBordersHashTable()->getIndexForHashCode($pStyle->getBorders()->getHashCode()));

        // Apply styles?
        $objWriter->writeAttribute('applyFont', ($spreadsheet->getDefaultStyle()->getFont()->getHashCode() != $pStyle->getFont()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyNumberFormat', ($spreadsheet->getDefaultStyle()->getNumberFormat()->getHashCode() != $pStyle->getNumberFormat()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyFill', ($spreadsheet->getDefaultStyle()->getFill()->getHashCode() != $pStyle->getFill()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyBorder', ($spreadsheet->getDefaultStyle()->getBorders()->getHashCode() != $pStyle->getBorders()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyAlignment', ($spreadsheet->getDefaultStyle()->getAlignment()->getHashCode() != $pStyle->getAlignment()->getHashCode()) ? '1' : '0');
        if ($pStyle->getProtection()->getLocked() != Protection::PROTECTION_INHERIT || $pStyle->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
            $objWriter->writeAttribute('applyProtection', 'true');
        }

        // alignment
        $objWriter->startElement('alignment');
        $objWriter->writeAttribute('horizontal', $pStyle->getAlignment()->getHorizontal());
        $objWriter->writeAttribute('vertical', $pStyle->getAlignment()->getVertical());

        $textRotation = 0;
        if ($pStyle->getAlignment()->getTextRotation() >= 0) {
            $textRotation = $pStyle->getAlignment()->getTextRotation();
        } elseif ($pStyle->getAlignment()->getTextRotation() < 0) {
            $textRotation = 90 - $pStyle->getAlignment()->getTextRotation();
        }
        $objWriter->writeAttribute('textRotation', $textRotation);

        $objWriter->writeAttribute('wrapText', ($pStyle->getAlignment()->getWrapText() ? 'true' : 'false'));
        $objWriter->writeAttribute('shrinkToFit', ($pStyle->getAlignment()->getShrinkToFit() ? 'true' : 'false'));

        if ($pStyle->getAlignment()->getIndent() > 0) {
            $objWriter->writeAttribute('indent', $pStyle->getAlignment()->getIndent());
        }
        if ($pStyle->getAlignment()->getReadorder() > 0) {
            $objWriter->writeAttribute('readingOrder', $pStyle->getAlignment()->getReadorder());
        }
        $objWriter->endElement();

        // protection
        if ($pStyle->getProtection()->getLocked() != Protection::PROTECTION_INHERIT || $pStyle->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
            $objWriter->startElement('protection');
            if ($pStyle->getProtection()->getLocked() != Protection::PROTECTION_INHERIT) {
                $objWriter->writeAttribute('locked', ($pStyle->getProtection()->getLocked() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
            }
            if ($pStyle->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
                $objWriter->writeAttribute('hidden', ($pStyle->getProtection()->getHidden() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
            }
            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    /**
     * Write Cell Style Dxf.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Style $pStyle Style
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeCellStyleDxf(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Style $pStyle)
    {
        // dxf
        $objWriter->startElement('dxf');

        // font
        $this->writeFont($objWriter, $pStyle->getFont());

        // numFmt
        $this->writeNumFmt($objWriter, $pStyle->getNumberFormat());

        // fill
        $this->writeFill($objWriter, $pStyle->getFill());

        // alignment
        $objWriter->startElement('alignment');
        if ($pStyle->getAlignment()->getHorizontal() !== null) {
            $objWriter->writeAttribute('horizontal', $pStyle->getAlignment()->getHorizontal());
        }
        if ($pStyle->getAlignment()->getVertical() !== null) {
            $objWriter->writeAttribute('vertical', $pStyle->getAlignment()->getVertical());
        }

        if ($pStyle->getAlignment()->getTextRotation() !== null) {
            $textRotation = 0;
            if ($pStyle->getAlignment()->getTextRotation() >= 0) {
                $textRotation = $pStyle->getAlignment()->getTextRotation();
            } elseif ($pStyle->getAlignment()->getTextRotation() < 0) {
                $textRotation = 90 - $pStyle->getAlignment()->getTextRotation();
            }
            $objWriter->writeAttribute('textRotation', $textRotation);
        }
        $objWriter->endElement();

        // border
        $this->writeBorder($objWriter, $pStyle->getBorders());

        // protection
        if (($pStyle->getProtection()->getLocked() !== null) || ($pStyle->getProtection()->getHidden() !== null)) {
            if ($pStyle->getProtection()->getLocked() !== Protection::PROTECTION_INHERIT ||
                $pStyle->getProtection()->getHidden() !== Protection::PROTECTION_INHERIT) {
                $objWriter->startElement('protection');
                if (($pStyle->getProtection()->getLocked() !== null) &&
                    ($pStyle->getProtection()->getLocked() !== Protection::PROTECTION_INHERIT)) {
                    $objWriter->writeAttribute('locked', ($pStyle->getProtection()->getLocked() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
                }
                if (($pStyle->getProtection()->getHidden() !== null) &&
                    ($pStyle->getProtection()->getHidden() !== Protection::PROTECTION_INHERIT)) {
                    $objWriter->writeAttribute('hidden', ($pStyle->getProtection()->getHidden() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
                }
                $objWriter->endElement();
            }
        }

        $objWriter->endElement();
    }

    /**
     * Write BorderPr.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param string $pName Element name
     * @param Border $pBorder Border style
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeBorderPr(XMLWriter $objWriter, $pName, Border $pBorder)
    {
        // Write BorderPr
        if ($pBorder->getBorderStyle() != Border::BORDER_NONE) {
            $objWriter->startElement($pName);
            $objWriter->writeAttribute('style', $pBorder->getBorderStyle());

            // color
            $objWriter->startElement('color');
            $objWriter->writeAttribute('rgb', $pBorder->getColor()->getARGB());
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    /**
     * Write NumberFormat.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param NumberFormat $pNumberFormat Number Format
     * @param int $pId Number Format identifier
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeNumFmt(XMLWriter $objWriter, NumberFormat $pNumberFormat, $pId = 0)
    {
        // Translate formatcode
        $formatCode = $pNumberFormat->getFormatCode();

        // numFmt
        if ($formatCode !== null) {
            $objWriter->startElement('numFmt');
            $objWriter->writeAttribute('numFmtId', ($pId + 164));
            $objWriter->writeAttribute('formatCode', $formatCode);
            $objWriter->endElement();
        }
    }

    /**
     * Get an array of all styles.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return \PhpOffice\PhpSpreadsheet\Style[] All styles in PhpSpreadsheet
     */
    public function allStyles(Spreadsheet $spreadsheet)
    {
        return $spreadsheet->getCellXfCollection();
    }

    /**
     * Get an array of all conditional styles.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return Conditional[] All conditional styles in PhpSpreadsheet
     */
    public function allConditionalStyles(Spreadsheet $spreadsheet)
    {
        // Get an array of all styles
        $aStyles = [];

        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            foreach ($spreadsheet->getSheet($i)->getConditionalStylesCollection() as $conditionalStyles) {
                foreach ($conditionalStyles as $conditionalStyle) {
                    $aStyles[] = $conditionalStyle;
                }
            }
        }

        return $aStyles;
    }

    /**
     * Get an array of all fills.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return Fill[] All fills in PhpSpreadsheet
     */
    public function allFills(Spreadsheet $spreadsheet)
    {
        // Get an array of unique fills
        $aFills = [];

        // Two first fills are predefined
        $fill0 = new Fill();
        $fill0->setFillType(Fill::FILL_NONE);
        $aFills[] = $fill0;

        $fill1 = new Fill();
        $fill1->setFillType(Fill::FILL_PATTERN_GRAY125);
        $aFills[] = $fill1;
        // The remaining fills
        $aStyles = $this->allStyles($spreadsheet);
        /** @var \PhpOffice\PhpSpreadsheet\Style $style */
        foreach ($aStyles as $style) {
            if (!isset($aFills[$style->getFill()->getHashCode()])) {
                $aFills[$style->getFill()->getHashCode()] = $style->getFill();
            }
        }

        return $aFills;
    }

    /**
     * Get an array of all fonts.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return Font[] All fonts in PhpSpreadsheet
     */
    public function allFonts(Spreadsheet $spreadsheet)
    {
        // Get an array of unique fonts
        $aFonts = [];
        $aStyles = $this->allStyles($spreadsheet);

        /** @var \PhpOffice\PhpSpreadsheet\Style $style */
        foreach ($aStyles as $style) {
            if (!isset($aFonts[$style->getFont()->getHashCode()])) {
                $aFonts[$style->getFont()->getHashCode()] = $style->getFont();
            }
        }

        return $aFonts;
    }

    /**
     * Get an array of all borders.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return Borders[] All borders in PhpSpreadsheet
     */
    public function allBorders(Spreadsheet $spreadsheet)
    {
        // Get an array of unique borders
        $aBorders = [];
        $aStyles = $this->allStyles($spreadsheet);

        /** @var \PhpOffice\PhpSpreadsheet\Style $style */
        foreach ($aStyles as $style) {
            if (!isset($aBorders[$style->getBorders()->getHashCode()])) {
                $aBorders[$style->getBorders()->getHashCode()] = $style->getBorders();
            }
        }

        return $aBorders;
    }

    /**
     * Get an array of all number formats.
     *
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return NumberFormat[] All number formats in PhpSpreadsheet
     */
    public function allNumberFormats(Spreadsheet $spreadsheet)
    {
        // Get an array of unique number formats
        $aNumFmts = [];
        $aStyles = $this->allStyles($spreadsheet);

        /** @var \PhpOffice\PhpSpreadsheet\Style $style */
        foreach ($aStyles as $style) {
            if ($style->getNumberFormat()->getBuiltInFormatCode() === false && !isset($aNumFmts[$style->getNumberFormat()->getHashCode()])) {
                $aNumFmts[$style->getNumberFormat()->getHashCode()] = $style->getNumberFormat();
            }
        }

        return $aNumFmts;
    }
}
