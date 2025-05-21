<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class Style extends WriterPart
{
    /**
     * Write styles to XML format.
     *
     * @return string XML Output
     */
    public function writeStyles(Spreadsheet $spreadsheet): string
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
        $objWriter->writeAttribute('xmlns', Namespaces::MAIN);

        // numFmts
        $objWriter->startElement('numFmts');
        $objWriter->writeAttribute('count', (string) $this->getParentWriter()->getNumFmtHashTable()->count());

        // numFmt
        for ($i = 0; $i < $this->getParentWriter()->getNumFmtHashTable()->count(); ++$i) {
            $this->writeNumFmt($objWriter, $this->getParentWriter()->getNumFmtHashTable()->getByIndex($i), $i);
        }

        $objWriter->endElement();

        // fonts
        $objWriter->startElement('fonts');
        $objWriter->writeAttribute('count', (string) $this->getParentWriter()->getFontHashTable()->count());

        // font
        for ($i = 0; $i < $this->getParentWriter()->getFontHashTable()->count(); ++$i) {
            $thisfont = $this->getParentWriter()->getFontHashTable()->getByIndex($i);
            if ($thisfont !== null) {
                $this->writeFont($objWriter, $thisfont);
            }
        }

        $objWriter->endElement();

        // fills
        $objWriter->startElement('fills');
        $objWriter->writeAttribute('count', (string) $this->getParentWriter()->getFillHashTable()->count());

        // fill
        for ($i = 0; $i < $this->getParentWriter()->getFillHashTable()->count(); ++$i) {
            $thisfill = $this->getParentWriter()->getFillHashTable()->getByIndex($i);
            if ($thisfill !== null) {
                $this->writeFill($objWriter, $thisfill);
            }
        }

        $objWriter->endElement();

        // borders
        $objWriter->startElement('borders');
        $objWriter->writeAttribute('count', (string) $this->getParentWriter()->getBordersHashTable()->count());

        // border
        for ($i = 0; $i < $this->getParentWriter()->getBordersHashTable()->count(); ++$i) {
            $thisborder = $this->getParentWriter()->getBordersHashTable()->getByIndex($i);
            if ($thisborder !== null) {
                $this->writeBorder($objWriter, $thisborder);
            }
        }

        $objWriter->endElement();

        // cellStyleXfs
        $objWriter->startElement('cellStyleXfs');
        $objWriter->writeAttribute('count', '1');

        // xf
        $objWriter->startElement('xf');
        $objWriter->writeAttribute('numFmtId', '0');
        $objWriter->writeAttribute('fontId', '0');
        $objWriter->writeAttribute('fillId', '0');
        $objWriter->writeAttribute('borderId', '0');
        $objWriter->endElement();

        $objWriter->endElement();

        // cellXfs
        $objWriter->startElement('cellXfs');
        $objWriter->writeAttribute('count', (string) count($spreadsheet->getCellXfCollection()));

        // xf
        $alignment = new Alignment();
        $defaultAlignHash = $alignment->getHashCode();
        if ($defaultAlignHash !== $spreadsheet->getDefaultStyle()->getAlignment()->getHashCode()) {
            $defaultAlignHash = '';
        }
        foreach ($spreadsheet->getCellXfCollection() as $cellXf) {
            $this->writeCellStyleXf($objWriter, $cellXf, $spreadsheet, $defaultAlignHash);
        }

        $objWriter->endElement();

        // cellStyles
        $objWriter->startElement('cellStyles');
        $objWriter->writeAttribute('count', '1');

        // cellStyle
        $objWriter->startElement('cellStyle');
        $objWriter->writeAttribute('name', 'Normal');
        $objWriter->writeAttribute('xfId', '0');
        $objWriter->writeAttribute('builtinId', '0');
        $objWriter->endElement();

        $objWriter->endElement();

        // dxfs
        $objWriter->startElement('dxfs');
        $objWriter->writeAttribute('count', (string) $this->getParentWriter()->getStylesConditionalHashTable()->count());

        // dxf
        for ($i = 0; $i < $this->getParentWriter()->getStylesConditionalHashTable()->count(); ++$i) {
            /** @var ?Conditional */
            $thisstyle = $this->getParentWriter()->getStylesConditionalHashTable()->getByIndex($i);
            if ($thisstyle !== null) {
                $this->writeCellStyleDxf($objWriter, $thisstyle->getStyle());
            }
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
     */
    private function writeFill(XMLWriter $objWriter, Fill $fill): void
    {
        // Check if this is a pattern type or gradient type
        if (
            $fill->getFillType() === Fill::FILL_GRADIENT_LINEAR
            || $fill->getFillType() === Fill::FILL_GRADIENT_PATH
        ) {
            // Gradient fill
            $this->writeGradientFill($objWriter, $fill);
        } elseif ($fill->getFillType() !== null) {
            // Pattern fill
            $this->writePatternFill($objWriter, $fill);
        }
    }

    /**
     * Write Gradient Fill.
     */
    private function writeGradientFill(XMLWriter $objWriter, Fill $fill): void
    {
        // fill
        $objWriter->startElement('fill');

        // gradientFill
        $objWriter->startElement('gradientFill');
        $objWriter->writeAttribute('type', (string) $fill->getFillType());
        $objWriter->writeAttribute('degree', (string) $fill->getRotation());

        // stop
        $objWriter->startElement('stop');
        $objWriter->writeAttribute('position', '0');

        // color
        if (!empty($fill->getStartColor()->getARGB())) {
            $objWriter->startElement('color');
            $objWriter->writeAttribute('rgb', $fill->getStartColor()->getARGB());
            $objWriter->endElement();
        }

        $objWriter->endElement();

        // stop
        $objWriter->startElement('stop');
        $objWriter->writeAttribute('position', '1');

        // color
        if (!empty($fill->getEndColor()->getARGB())) {
            $objWriter->startElement('color');
            $objWriter->writeAttribute('rgb', $fill->getEndColor()->getARGB());
            $objWriter->endElement();
        }

        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();
    }

    private static function writePatternColors(Fill $fill): bool
    {
        if ($fill->getFillType() === Fill::FILL_NONE) {
            return false;
        }

        return $fill->getFillType() === Fill::FILL_SOLID || $fill->getColorsChanged();
    }

    /**
     * Write Pattern Fill.
     */
    private function writePatternFill(XMLWriter $objWriter, Fill $fill): void
    {
        // fill
        $objWriter->startElement('fill');

        // patternFill
        $objWriter->startElement('patternFill');
        if ($fill->getFillType()) {
            $objWriter->writeAttribute('patternType', (string) $fill->getFillType());
        }

        if (self::writePatternColors($fill)) {
            // fgColor
            if ($fill->getStartColor()->getARGB()) {
                if (!$fill->getEndColor()->getARGB() && $fill->getFillType() === Fill::FILL_SOLID) {
                    $objWriter->startElement('bgColor');
                    $objWriter->writeAttribute('rgb', $fill->getStartColor()->getARGB());
                } else {
                    $objWriter->startElement('fgColor');
                    $objWriter->writeAttribute('rgb', $fill->getStartColor()->getARGB());
                }
                $objWriter->endElement();
            }
            // bgColor
            if ($fill->getEndColor()->getARGB()) {
                $objWriter->startElement('bgColor');
                $objWriter->writeAttribute('rgb', $fill->getEndColor()->getARGB());
                $objWriter->endElement();
            }
        }

        $objWriter->endElement();

        $objWriter->endElement();
    }

    private function startFont(XMLWriter $objWriter, bool &$fontStarted): void
    {
        if (!$fontStarted) {
            $fontStarted = true;
            $objWriter->startElement('font');
        }
    }

    /**
     * Write Font.
     */
    private function writeFont(XMLWriter $objWriter, Font $font): void
    {
        $fontStarted = false;
        // font
        //    Weird! The order of these elements actually makes a difference when opening Xlsx
        //        files in Excel2003 with the compatibility pack. It's not documented behaviour,
        //        and makes for a real WTF!

        // Bold. We explicitly write this element also when false (like MS Office Excel 2007 does
        // for conditional formatting). Otherwise it will apparently not be picked up in conditional
        // formatting style dialog
        if ($font->getBold() !== null) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('b');
            $objWriter->writeAttribute('val', $font->getBold() ? '1' : '0');
            $objWriter->endElement();
        }

        // Italic
        if ($font->getItalic() !== null) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('i');
            $objWriter->writeAttribute('val', $font->getItalic() ? '1' : '0');
            $objWriter->endElement();
        }

        // Strikethrough
        if ($font->getStrikethrough() !== null) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('strike');
            $objWriter->writeAttribute('val', $font->getStrikethrough() ? '1' : '0');
            $objWriter->endElement();
        }

        // Underline
        if ($font->getUnderline() !== null) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('u');
            $objWriter->writeAttribute('val', $font->getUnderline());
            $objWriter->endElement();
        }

        // Superscript / subscript
        if ($font->getSuperscript() === true || $font->getSubscript() === true) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('vertAlign');
            if ($font->getSuperscript() === true) {
                $objWriter->writeAttribute('val', 'superscript');
            } elseif ($font->getSubscript() === true) {
                $objWriter->writeAttribute('val', 'subscript');
            }
            $objWriter->endElement();
        }

        // Size
        if ($font->getSize() !== null) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('sz');
            $objWriter->writeAttribute('val', StringHelper::formatNumber($font->getSize()));
            $objWriter->endElement();
        }

        // Foreground color
        if ($font->getColor()->getTheme() >= 0) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('color');
            $objWriter->writeAttribute('theme', (string) $font->getColor()->getTheme());
            $objWriter->endElement();
        } elseif ($font->getColor()->getARGB() !== null) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('color');
            $objWriter->writeAttribute('rgb', $font->getColor()->getARGB());
            $objWriter->endElement();
        }

        // Name
        if ($font->getName() !== null) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('name');
            $objWriter->writeAttribute('val', $font->getName());
            $objWriter->endElement();
        }

        if (!empty($font->getScheme())) {
            $this->startFont($objWriter, $fontStarted);
            $objWriter->startElement('scheme');
            $objWriter->writeAttribute('val', $font->getScheme());
            $objWriter->endElement();
        }

        if ($fontStarted) {
            $objWriter->endElement();
        }
    }

    /**
     * Write Border.
     */
    private function writeBorder(XMLWriter $objWriter, Borders $borders): void
    {
        // Write border
        $objWriter->startElement('border');
        // Diagonal?
        switch ($borders->getDiagonalDirection()) {
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
        $this->writeBorderPr($objWriter, 'left', $borders->getLeft());
        $this->writeBorderPr($objWriter, 'right', $borders->getRight());
        $this->writeBorderPr($objWriter, 'top', $borders->getTop());
        $this->writeBorderPr($objWriter, 'bottom', $borders->getBottom());
        $this->writeBorderPr($objWriter, 'diagonal', $borders->getDiagonal());
        $objWriter->endElement();
    }

    /**
     * Write Cell Style Xf.
     */
    private function writeCellStyleXf(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Style\Style $style, Spreadsheet $spreadsheet, string $defaultAlignHash): void
    {
        // xf
        $objWriter->startElement('xf');
        $objWriter->writeAttribute('xfId', '0');
        $objWriter->writeAttribute('fontId', (string) (int) $this->getParentWriter()->getFontHashTable()->getIndexForHashCode($style->getFont()->getHashCode()));
        if ($style->getQuotePrefix()) {
            $objWriter->writeAttribute('quotePrefix', '1');
        }

        if ($style->getNumberFormat()->getBuiltInFormatCode() === false) {
            $objWriter->writeAttribute('numFmtId', (string) (int) ($this->getParentWriter()->getNumFmtHashTable()->getIndexForHashCode($style->getNumberFormat()->getHashCode()) + 164));
        } else {
            $objWriter->writeAttribute('numFmtId', (string) (int) $style->getNumberFormat()->getBuiltInFormatCode());
        }

        $objWriter->writeAttribute('fillId', (string) (int) $this->getParentWriter()->getFillHashTable()->getIndexForHashCode($style->getFill()->getHashCode()));
        $objWriter->writeAttribute('borderId', (string) (int) $this->getParentWriter()->getBordersHashTable()->getIndexForHashCode($style->getBorders()->getHashCode()));

        // Apply styles?
        $objWriter->writeAttribute('applyFont', ($spreadsheet->getDefaultStyle()->getFont()->getHashCode() != $style->getFont()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyNumberFormat', ($spreadsheet->getDefaultStyle()->getNumberFormat()->getHashCode() != $style->getNumberFormat()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyFill', ($spreadsheet->getDefaultStyle()->getFill()->getHashCode() != $style->getFill()->getHashCode()) ? '1' : '0');
        $objWriter->writeAttribute('applyBorder', ($spreadsheet->getDefaultStyle()->getBorders()->getHashCode() != $style->getBorders()->getHashCode()) ? '1' : '0');
        if ($defaultAlignHash !== '' && $defaultAlignHash === $style->getAlignment()->getHashCode()) {
            $applyAlignment = '0';
        } else {
            $applyAlignment = '1';
        }
        $objWriter->writeAttribute('applyAlignment', $applyAlignment);
        if ($style->getProtection()->getLocked() != Protection::PROTECTION_INHERIT || $style->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
            $objWriter->writeAttribute('applyProtection', 'true');
        }

        // alignment
        if ($applyAlignment === '1') {
            $objWriter->startElement('alignment');
            $vertical = Alignment::VERTICAL_ALIGNMENT_FOR_XLSX[$style->getAlignment()->getVertical()] ?? '';
            $horizontal = Alignment::HORIZONTAL_ALIGNMENT_FOR_XLSX[$style->getAlignment()->getHorizontal()] ?? '';
            if ($horizontal !== '') {
                $objWriter->writeAttribute('horizontal', $horizontal);
            }
            if ($vertical !== '') {
                $objWriter->writeAttribute('vertical', $vertical);
            }
            $justifyLastLine = $style->getAlignment()->getJustifyLastLine();
            if (is_bool($justifyLastLine)) {
                $objWriter->writeAttribute('justifyLastLine', (string) (int) $justifyLastLine);
            }

            if ($style->getAlignment()->getTextRotation() >= 0) {
                $textRotation = $style->getAlignment()->getTextRotation();
            } else {
                $textRotation = 90 - $style->getAlignment()->getTextRotation();
            }
            $objWriter->writeAttribute('textRotation', (string) $textRotation);

            $objWriter->writeAttribute('wrapText', ($style->getAlignment()->getWrapText() ? 'true' : 'false'));
            $objWriter->writeAttribute('shrinkToFit', ($style->getAlignment()->getShrinkToFit() ? 'true' : 'false'));

            if ($style->getAlignment()->getIndent() > 0) {
                $objWriter->writeAttribute('indent', (string) $style->getAlignment()->getIndent());
            }
            if ($style->getAlignment()->getReadOrder() > 0) {
                $objWriter->writeAttribute('readingOrder', (string) $style->getAlignment()->getReadOrder());
            }
            $objWriter->endElement();
        }

        // protection
        if ($style->getProtection()->getLocked() != Protection::PROTECTION_INHERIT || $style->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
            $objWriter->startElement('protection');
            if ($style->getProtection()->getLocked() != Protection::PROTECTION_INHERIT) {
                $objWriter->writeAttribute('locked', ($style->getProtection()->getLocked() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
            }
            if ($style->getProtection()->getHidden() != Protection::PROTECTION_INHERIT) {
                $objWriter->writeAttribute('hidden', ($style->getProtection()->getHidden() == Protection::PROTECTION_PROTECTED ? 'true' : 'false'));
            }
            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    /**
     * Write Cell Style Dxf.
     */
    private function writeCellStyleDxf(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Style\Style $style): void
    {
        // dxf
        $objWriter->startElement('dxf');

        // font
        $this->writeFont($objWriter, $style->getFont());

        // numFmt
        $this->writeNumFmt($objWriter, $style->getNumberFormat());

        // fill
        $this->writeFill($objWriter, $style->getFill());

        // border
        $this->writeBorder($objWriter, $style->getBorders());

        $objWriter->endElement();
    }

    /**
     * Write BorderPr.
     *
     * @param string $name Element name
     */
    private function writeBorderPr(XMLWriter $objWriter, string $name, Border $border): void
    {
        // Write BorderPr
        if ($border->getBorderStyle() === Border::BORDER_OMIT) {
            return;
        }
        $objWriter->startElement($name);
        if ($border->getBorderStyle() !== Border::BORDER_NONE) {
            $objWriter->writeAttribute('style', $border->getBorderStyle());

            // color
            if ($border->getColor()->getARGB() !== null) {
                $objWriter->startElement('color');
                $objWriter->writeAttribute('rgb', $border->getColor()->getARGB());
                $objWriter->endElement();
            }
        }
        $objWriter->endElement();
    }

    /**
     * Write NumberFormat.
     *
     * @param int $id Number Format identifier
     */
    private function writeNumFmt(XMLWriter $objWriter, ?NumberFormat $numberFormat, int $id = 0): void
    {
        // Translate formatcode
        $formatCode = ($numberFormat === null) ? null : $numberFormat->getFormatCode();

        // numFmt
        if ($formatCode !== null) {
            $objWriter->startElement('numFmt');
            $objWriter->writeAttribute('numFmtId', (string) ($id + 164));
            $objWriter->writeAttribute('formatCode', $formatCode);
            $objWriter->endElement();
        }
    }

    /**
     * Get an array of all styles.
     *
     * @return \PhpOffice\PhpSpreadsheet\Style\Style[] All styles in PhpSpreadsheet
     */
    public function allStyles(Spreadsheet $spreadsheet): array
    {
        return $spreadsheet->getCellXfCollection();
    }

    /**
     * Get an array of all conditional styles.
     *
     * @return Conditional[] All conditional styles in PhpSpreadsheet
     */
    public function allConditionalStyles(Spreadsheet $spreadsheet): array
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
     * @return Fill[] All fills in PhpSpreadsheet
     */
    public function allFills(Spreadsheet $spreadsheet): array
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
     * @return Font[] All fonts in PhpSpreadsheet
     */
    public function allFonts(Spreadsheet $spreadsheet): array
    {
        // Get an array of unique fonts
        $aFonts = [];
        $aStyles = $this->allStyles($spreadsheet);

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
     * @return Borders[] All borders in PhpSpreadsheet
     */
    public function allBorders(Spreadsheet $spreadsheet): array
    {
        // Get an array of unique borders
        $aBorders = [];
        $aStyles = $this->allStyles($spreadsheet);

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
     * @return NumberFormat[] All number formats in PhpSpreadsheet
     */
    public function allNumberFormats(Spreadsheet $spreadsheet): array
    {
        // Get an array of unique number formats
        $aNumFmts = [];
        $aStyles = $this->allStyles($spreadsheet);

        foreach ($aStyles as $style) {
            if ($style->getNumberFormat()->getBuiltInFormatCode() === false && !isset($aNumFmts[$style->getNumberFormat()->getHashCode()])) {
                $aNumFmts[$style->getNumberFormat()->getHashCode()] = $style->getNumberFormat();
            }
        }

        return $aNumFmts;
    }
}
