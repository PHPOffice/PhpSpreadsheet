<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods\Cell;

use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
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
    public const INDENT_TO_INCHES = 0.1043; // undocumented, used trial and error

    private XMLWriter $writer;

    public function __construct(XMLWriter $writer)
    {
        $this->writer = $writer;
    }

    private function mapHorizontalAlignment(?string $horizontalAlignment): string
    {
        return match ($horizontalAlignment) {
            Alignment::HORIZONTAL_CENTER, Alignment::HORIZONTAL_CENTER_CONTINUOUS, Alignment::HORIZONTAL_DISTRIBUTED => 'center',
            Alignment::HORIZONTAL_RIGHT => 'end',
            Alignment::HORIZONTAL_FILL, Alignment::HORIZONTAL_JUSTIFY => 'justify',
            Alignment::HORIZONTAL_GENERAL, '', null => '',
            default => 'start',
        };
    }

    private function mapVerticalAlignment(string $verticalAlignment): string
    {
        return match ($verticalAlignment) {
            Alignment::VERTICAL_TOP => 'top',
            Alignment::VERTICAL_CENTER => 'middle',
            Alignment::VERTICAL_DISTRIBUTED, Alignment::VERTICAL_JUSTIFY => 'automatic',
            default => 'bottom',
        };
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

    private function writeBordersStyle(Borders $borders): void
    {
        $this->writeBorderStyle('bottom', $borders->getBottom());
        $this->writeBorderStyle('left', $borders->getLeft());
        $this->writeBorderStyle('right', $borders->getRight());
        $this->writeBorderStyle('top', $borders->getTop());
    }

    private function writeBorderStyle(string $direction, Border $border): void
    {
        if ($border->getBorderStyle() === Border::BORDER_NONE) {
            return;
        }

        $this->writer->writeAttribute('fo:border-' . $direction, sprintf(
            '%s %s #%s',
            $this->mapBorderWidth($border),
            $this->mapBorderStyle($border),
            $border->getColor()->getRGB(),
        ));
    }

    private function mapBorderWidth(Border $border): string
    {
        switch ($border->getBorderStyle()) {
            case Border::BORDER_THIN:
            case Border::BORDER_DASHED:
            case Border::BORDER_DASHDOT:
            case Border::BORDER_DASHDOTDOT:
            case Border::BORDER_DOTTED:
            case Border::BORDER_HAIR:
                return '0.75pt';
            case Border::BORDER_MEDIUM:
            case Border::BORDER_MEDIUMDASHED:
            case Border::BORDER_MEDIUMDASHDOT:
            case Border::BORDER_MEDIUMDASHDOTDOT:
            case Border::BORDER_SLANTDASHDOT:
                return '1.75pt';
            case Border::BORDER_DOUBLE:
            case Border::BORDER_THICK:
                return '2.5pt';
        }

        return '1pt';
    }

    private function mapBorderStyle(Border $border): string
    {
        switch ($border->getBorderStyle()) {
            case Border::BORDER_DOTTED:
            case Border::BORDER_MEDIUMDASHDOTDOT:
                return Border::BORDER_DOTTED;

            case Border::BORDER_DASHED:
            case Border::BORDER_DASHDOT:
            case Border::BORDER_DASHDOTDOT:
            case Border::BORDER_MEDIUMDASHDOT:
            case Border::BORDER_MEDIUMDASHED:
            case Border::BORDER_SLANTDASHDOT:
                return Border::BORDER_DASHED;

            case Border::BORDER_DOUBLE:
                return Border::BORDER_DOUBLE;

            case Border::BORDER_HAIR:
            case Border::BORDER_MEDIUM:
            case Border::BORDER_THICK:
            case Border::BORDER_THIN:
                return 'solid';
        }

        return 'solid';
    }

    private function writeCellProperties(CellStyle $style): void
    {
        // Align
        $hAlign = $style->getAlignment()->getHorizontal();
        $hAlign = $this->mapHorizontalAlignment($hAlign);
        $vAlign = $style->getAlignment()->getVertical();
        $wrap = $style->getAlignment()->getWrapText();
        $indent = $style->getAlignment()->getIndent();
        $readOrder = $style->getAlignment()->getReadOrder();

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
        $this->writeFillStyle($style->getFill());

        // Border
        $this->writeBordersStyle($style->getBorders());

        $this->writer->endElement();

        if ($hAlign !== '' || !empty($indent) || $readOrder === Alignment::READORDER_RTL || $readOrder === Alignment::READORDER_LTR) {
            $this->writer
                ->startElement('style:paragraph-properties');
            if ($hAlign !== '') {
                $this->writer->writeAttribute('fo:text-align', $hAlign);
            }
            if (!empty($indent)) {
                $indentString = sprintf('%.4f', $indent * self::INDENT_TO_INCHES) . 'in';
                $this->writer->writeAttribute('fo:margin-left', $indentString);
            }
            if ($readOrder === Alignment::READORDER_RTL) {
                $this->writer->writeAttribute('style:writing-mode', 'rl-tb');
            } elseif ($readOrder === Alignment::READORDER_LTR) {
                $this->writer->writeAttribute('style:writing-mode', 'lr-tb');
            }
            $this->writer->endElement();
        }
    }

    protected function mapUnderlineStyle(Font $font): string
    {
        return match ($font->getUnderline()) {
            Font::UNDERLINE_DOUBLE, Font::UNDERLINE_DOUBLEACCOUNTING => 'double',
            Font::UNDERLINE_SINGLE, Font::UNDERLINE_SINGLEACCOUNTING => 'single',
            default => 'none',
        };
    }

    protected function writeTextProperties(CellStyle $style): void
    {
        // Font
        $this->writer->startElement('style:text-properties');

        $font = $style->getFont();

        if ($font->getBold()) {
            $this->writer->writeAttribute('fo:font-weight', 'bold');
            $this->writer->writeAttribute(
                'style:font-weight-complex',
                'bold'
            );
            $this->writer->writeAttribute(
                'style:font-weight-asian',
                'bold'
            );
        }

        if ($font->getItalic()) {
            $this->writer->writeAttribute('fo:font-style', 'italic');
        }

        if ($font->getAutoColor()) {
            $this->writer
                ->writeAttribute('style:use-window-font-color', 'true');
        } else {
            $this->writer->writeAttribute('fo:color', sprintf('#%s', $font->getColor()->getRGB()));
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

    public function writeDefaultRowStyle(RowDimension $rowDimension, int $sheetId): void
    {
        $this->writer->startElement('style:style');
        $this->writer->writeAttribute('style:family', 'table-row');
        $this->writer->writeAttribute(
            'style:name',
            sprintf('%s%d', self::ROW_STYLE_PREFIX, $sheetId)
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
        $this->writer->writeAttribute('style:master-page-name', 'Default');

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
