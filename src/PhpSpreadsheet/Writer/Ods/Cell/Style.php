<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods\Cell;

use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style as CellStyle;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\RowDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Style
{
    public const CELL_STYLE_PREFIX = 'ce';
    public const COLUMN_STYLE_PREFIX = 'co';
    public const ROW_STYLE_PREFIX = 'ro';
    public const TABLE_STYLE_PREFIX = 'ta';

    private $writer;

    public function __construct(XMLWriter $writer)
    {
        $this->writer = $writer;
    }

    private function mapHorizontalAlignment(string $horizontalAlignment): string
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

    private function mapVerticalAlignment(string $verticalAlignment): string
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

    private function writeFillStyle(Fill $fill): void
    {
        switch ($fill->getFillType()) {
            case Fill::FILL_SOLID:
                $this->writer->writeAttribute('fo:background-color', sprintf(
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

    private function writeCellProperties(CellStyle $style): void
    {
        // Align
        $hAlign = $style->getAlignment()->getHorizontal();
        $vAlign = $style->getAlignment()->getVertical();
        $wrap = $style->getAlignment()->getWrapText();

        $this->writer->startElement('style:table-cell-properties');
        if (!empty($vAlign) || $wrap) {
            if (!empty($vAlign)) {
                $vAlign = $this->mapVerticalAlignment($vAlign);
                $this->writer->writeAttribute('style:vertical-align', $vAlign);
            }
            if ($wrap) {
                $this->writer->writeAttribute('fo:wrap-option', 'wrap');
            }
        }
        $this->writer->writeAttribute('style:rotation-align', 'none');

        // Fill
        if ($fill = $style->getFill()) {
            $this->writeFillStyle($fill);
        }

        $this->writer->endElement();

        if (!empty($hAlign)) {
            $hAlign = $this->mapHorizontalAlignment($hAlign);
            $this->writer->startElement('style:paragraph-properties');
            $this->writer->writeAttribute('fo:text-align', $hAlign);
            $this->writer->endElement();
        }
    }

    protected function mapUnderlineStyle(Font $font): string
    {
        switch ($font->getUnderline()) {
            case Font::UNDERLINE_DOUBLE:
            case Font::UNDERLINE_DOUBLEACCOUNTING:
                return'double';
            case Font::UNDERLINE_SINGLE:
            case Font::UNDERLINE_SINGLEACCOUNTING:
                return'single';
        }

        return 'none';
    }

    protected function writeTextProperties(CellStyle $style): void
    {
        // Font
        $this->writer->startElement('style:text-properties');

        $font = $style->getFont();

        if ($font->getBold()) {
            $this->writer->writeAttribute('fo:font-weight', 'bold');
            $this->writer->writeAttribute('style:font-weight-complex', 'bold');
            $this->writer->writeAttribute('style:font-weight-asian', 'bold');
        }

        if ($font->getItalic()) {
            $this->writer->writeAttribute('fo:font-style', 'italic');
        }

        if ($color = $font->getColor()) {
            $this->writer->writeAttribute('fo:color', sprintf('#%s', $color->getRGB()));
        }

        if ($family = $font->getName()) {
            $this->writer->writeAttribute('fo:font-family', $family);
        }

        if ($size = $font->getSize()) {
            $this->writer->writeAttribute('fo:font-size', sprintf('%.1Fpt', $size));
        }

        if ($font->getUnderline() && $font->getUnderline() !== Font::UNDERLINE_NONE) {
            $this->writer->writeAttribute('style:text-underline-style', 'solid');
            $this->writer->writeAttribute('style:text-underline-width', 'auto');
            $this->writer->writeAttribute('style:text-underline-color', 'font-color');

            $underline = $this->mapUnderlineStyle($font);
            $this->writer->writeAttribute('style:text-underline-type', $underline);
        }

        $this->writer->endElement(); // Close style:text-properties
    }

    protected function writeColumnProperties(ColumnDimension $columnDimension): void
    {
        $this->writer->startElement('style:table-column-properties');
        $this->writer->writeAttribute(
            'style:column-width',
            round($columnDimension->getWidth(Dimension::UOM_CENTIMETERS), 3) . 'cm'
        );
        $this->writer->writeAttribute('fo:break-before', 'auto');

        // End
        $this->writer->endElement(); // Close style:table-column-properties
    }

    public function writeColumnStyles(ColumnDimension $columnDimension, int $sheetId): void
    {
        $this->writer->startElement('style:style');
        $this->writer->writeAttribute('style:family', 'table-column');
        $this->writer->writeAttribute(
            'style:name',
            sprintf('%s_%d_%d', self::COLUMN_STYLE_PREFIX, $sheetId, $columnDimension->getColumnNumeric())
        );

        $this->writeColumnProperties($columnDimension);

        // End
        $this->writer->endElement(); // Close style:style
    }

    protected function writeRowProperties(RowDimension $rowDimension): void
    {
        $this->writer->startElement('style:table-row-properties');
        $this->writer->writeAttribute(
            'style:row-height',
            round($rowDimension->getRowHeight(Dimension::UOM_CENTIMETERS), 3) . 'cm'
        );
        $this->writer->writeAttribute('style:use-optimal-row-height', 'false');
        $this->writer->writeAttribute('fo:break-before', 'auto');

        // End
        $this->writer->endElement(); // Close style:table-row-properties
    }

    public function writeRowStyles(RowDimension $rowDimension, int $sheetId): void
    {
        $this->writer->startElement('style:style');
        $this->writer->writeAttribute('style:family', 'table-row');
        $this->writer->writeAttribute(
            'style:name',
            sprintf('%s_%d_%d', self::ROW_STYLE_PREFIX, $sheetId, $rowDimension->getRowIndex())
        );

        $this->writeRowProperties($rowDimension);

        // End
        $this->writer->endElement(); // Close style:style
    }

    public function writeTableStyle(Worksheet $worksheet, int $sheetId): void
    {
        $this->writer->startElement('style:style');
        $this->writer->writeAttribute('style:family', 'table');
        $this->writer->writeAttribute(
            'style:name',
            sprintf('%s%d', self::TABLE_STYLE_PREFIX, $sheetId)
        );

        $this->writer->startElement('style:table-properties');

        $this->writer->writeAttribute(
            'table:display',
            $worksheet->getSheetState() === Worksheet::SHEETSTATE_VISIBLE ? 'true' : 'false'
        );

        $this->writer->endElement(); // Close style:table-properties
        $this->writer->endElement(); // Close style:style
    }

    public function write(CellStyle $style): void
    {
        $this->writer->startElement('style:style');
        $this->writer->writeAttribute('style:name', self::CELL_STYLE_PREFIX . $style->getIndex());
        $this->writer->writeAttribute('style:family', 'table-cell');
        $this->writer->writeAttribute('style:parent-style-name', 'Default');

        // Alignment, fill colour, etc
        $this->writeCellProperties($style);

        // style:text-properties
        $this->writeTextProperties($style);

        // End
        $this->writer->endElement(); // Close style:style
    }
}
