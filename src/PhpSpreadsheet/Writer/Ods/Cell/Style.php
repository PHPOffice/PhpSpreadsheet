<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods\Cell;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style as CellStyle;

class Style
{
    public const CELL_STYLE_PREFIX = 'ce';

    private static function mapHorizontalAlignment(string $horizontalAlignment): string
    {
        switch ($horizontalAlignment) {
            case Alignment::HORIZONTAL_CENTER:
            case Alignment::HORIZONTAL_CENTER_CONTINUOUS:
            case Alignment::HORIZONTAL_DISTRIBUTED:
                return 'center';
            case Alignment::HORIZONTAL_RIGHT:
                return 'end';
            case Alignment::HORIZONTAL_FILL:
            case Alignment::HORIZONTAL_JUSTIFY:
                return 'justify';
        }

        return 'start';
    }

    private static function mapVerticalAlignment(string $verticalAlignment): string
    {
        switch ($verticalAlignment) {
            case Alignment::VERTICAL_TOP:
                return 'top';
            case Alignment::VERTICAL_CENTER:
                return 'middle';
            case Alignment::VERTICAL_DISTRIBUTED:
            case Alignment::VERTICAL_JUSTIFY:
                return 'automatic';
        }

        return 'bottom';
    }

    public static function writeFillStyle(XMLWriter $writer, Fill $fill): void
    {
        switch ($fill->getFillType()) {
            case Fill::FILL_SOLID:
                $writer->writeAttribute('fo:background-color', sprintf(
                    '#%s',
                    strtolower($fill->getStartColor()->getRGB())
                ));

                break;
            case Fill::FILL_GRADIENT_LINEAR:
            case Fill::FILL_GRADIENT_PATH:
                /// TODO :: To be implemented
                break;
            case Fill::FILL_NONE:
            default:
        }
    }

    public static function write(XMLWriter $writer, CellStyle $style): void
    {
        $writer->startElement('style:style');
        $writer->writeAttribute('style:name', self::CELL_STYLE_PREFIX . $style->getIndex());
        $writer->writeAttribute('style:family', 'table-cell');
        $writer->writeAttribute('style:parent-style-name', 'Default');

        // Align
        $hAlign = $style->getAlignment()->getHorizontal();
        $vAlign = $style->getAlignment()->getVertical();
        $wrap = $style->getAlignment()->getWrapText();

        $writer->startElement('style:table-cell-properties');
        if (!empty($vAlign) || $wrap) {
            if (!empty($vAlign)) {
                $vAlign = self::mapVerticalAlignment($vAlign);
                $writer->writeAttribute('style:vertical-align', $vAlign);
            }
            if ($wrap) {
                $writer->writeAttribute('fo:wrap-option', 'wrap');
            }
        }
        $writer->writeAttribute('style:rotation-align', 'none');

        // Fill
        if ($fill = $style->getFill()) {
            self::writeFillStyle($writer, $fill);
        }

        $writer->endElement();

        if (!empty($hAlign)) {
            $hAlign = self::mapHorizontalAlignment($hAlign);
            $writer->startElement('style:paragraph-properties');
            $writer->writeAttribute('fo:text-align', $hAlign);
            $writer->endElement();
        }

        // style:text-properties

        // Font
        $writer->startElement('style:text-properties');

        $font = $style->getFont();

        if ($font->getBold()) {
            $writer->writeAttribute('fo:font-weight', 'bold');
            $writer->writeAttribute('style:font-weight-complex', 'bold');
            $writer->writeAttribute('style:font-weight-asian', 'bold');
        }

        if ($font->getItalic()) {
            $writer->writeAttribute('fo:font-style', 'italic');
        }

        if ($color = $font->getColor()) {
            $writer->writeAttribute('fo:color', sprintf('#%s', $color->getRGB()));
        }

        if ($family = $font->getName()) {
            $writer->writeAttribute('fo:font-family', $family);
        }

        if ($size = $font->getSize()) {
            $writer->writeAttribute('fo:font-size', sprintf('%.1Fpt', $size));
        }

        if ($font->getUnderline() && $font->getUnderline() != Font::UNDERLINE_NONE) {
            $writer->writeAttribute('style:text-underline-style', 'solid');
            $writer->writeAttribute('style:text-underline-width', 'auto');
            $writer->writeAttribute('style:text-underline-color', 'font-color');

            switch ($font->getUnderline()) {
                case Font::UNDERLINE_DOUBLE:
                    $writer->writeAttribute('style:text-underline-type', 'double');

                    break;
                case Font::UNDERLINE_SINGLE:
                    $writer->writeAttribute('style:text-underline-type', 'single');

                    break;
            }
        }

        $writer->endElement(); // Close style:text-properties

        // End
        $writer->endElement(); // Close style:style
    }
}
