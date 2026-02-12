<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods\Cell;

use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
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

    /** @var array<string, callable> */
    private array $additionalNumberFormats;

    /** @param array<string, callable> $additionalNumberFormats */
    public function __construct(XMLWriter $writer, array $additionalNumberFormats = [])
    {
        $this->writer = $writer;
        $this->additionalNumberFormats = $additionalNumberFormats;
    }

    public function getWriter(): XMLWriter
    {
        return $this->writer;
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

    private int $numFmtIndex = 199;

    /** @var array<string, string> */
    private array $numFmtIndexes = [];

    private function writeNumFmt(string $numFmt): void
    {
        if (array_key_exists($numFmt, $this->numFmtIndexes)) {
            return;
        }
        $method = $this->additionalNumberFormats[$numFmt] ?? self::NUMBER_FORMAT_METHODS[$numFmt] ?? null;
        if ($method === null) {
            return;
        }
        ++$this->numFmtIndex;
        $name = 'N' . $this->numFmtIndex;
        $this->numFmtIndexes[$numFmt] = $name;
        $method($this, $name);
    }

    public function write(CellStyle $style): void
    {
        $numFmt = (string) $style->getNumberFormat()->getFormatCode();
        $this->writeNumFmt($numFmt);
        $this->writer->startElement('style:style');
        $this->writer->writeAttribute('style:name', self::CELL_STYLE_PREFIX . $style->getIndex());
        $this->writer->writeAttribute('style:family', 'table-cell');
        $this->writer->writeAttribute('style:parent-style-name', 'Default');
        if (array_key_exists($numFmt, $this->numFmtIndexes)) {
            $this->writer->writeAttribute(
                'style:data-style-name',
                $this->numFmtIndexes[$numFmt]
            );
        }

        // Alignment, fill colour, etc
        $this->writeCellProperties($style);

        // style:text-properties
        $this->writeTextProperties($style);

        // End
        $this->writer->endElement(); // Close style:style
    }

    private const NUMBER_FORMAT_METHODS = [
        NumberFormat::FORMAT_NUMBER => [self::class, 'formatNumber'],
        NumberFormat::FORMAT_NUMBER_0 => [self::class, 'formatNumber0'],
        NumberFormat::FORMAT_NUMBER_00 => [self::class, 'formatNumber00'],
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1 => [self::class, 'formatNumberCommaSeparated1'],
        NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2 => [self::class, 'formatNumberCommaSeparated2'],
        NumberFormat::FORMAT_PERCENTAGE => [self::class, 'formatPercentage'],
        NumberFormat::FORMAT_PERCENTAGE_0 => [self::class, 'formatPercentage0'],
        NumberFormat::FORMAT_PERCENTAGE_00 => [self::class, 'formatPercentage00'],
        NumberFormat::FORMAT_DATE_YYYYMMDD => [self::class, 'formatDateYyyymmdd'],
        NumberFormat::FORMAT_DATE_DDMMYYYY => [self::class, 'formatDateDdmmyyyy'],
        NumberFormat::FORMAT_DATE_DMYSLASH => [self::class, 'formatDateDmyslash'],
        NumberFormat::FORMAT_DATE_DMYMINUS => [self::class, 'formatDateDmyminus'],
        NumberFormat::FORMAT_DATE_DMMINUS => [self::class, 'formatDateDmminus'],
        NumberFormat::FORMAT_DATE_MYMINUS => [self::class, 'formatDateMyminus'],
        NumberFormat::FORMAT_DATE_XLSX14 => [self::class, 'formatDateXlsx14'],
        NumberFormat::FORMAT_DATE_XLSX14_ACTUAL => [self::class, 'formatDateXlsx14Actual'],
        NumberFormat::FORMAT_DATE_XLSX15 => [self::class, 'formatDateXlsx15'],
        NumberFormat::FORMAT_DATE_XLSX15_YYYY => [self::class, 'formatDateXlsx15Yyyy'],
        NumberFormat::FORMAT_DATE_XLSX16 => [self::class, 'formatDateXlsx16'],
        NumberFormat::FORMAT_DATE_XLSX17 => [self::class, 'formatDateXlsx17'],
        NumberFormat::FORMAT_DATE_XLSX22 => [self::class, 'formatDateXlsx22'],
        NumberFormat::FORMAT_DATE_XLSX22_ACTUAL => [self::class, 'formatDateXlsx22Actual'],
        NumberFormat::FORMAT_DATE_DATETIME => [self::class, 'formatDateDatetime'],
        NumberFormat::FORMAT_DATE_DATETIME_BETTER => [self::class, 'formatDateDatetimeBetter'],
        NumberFormat::FORMAT_DATE_TIME1 => [self::class, 'formatDateTime1'],
        NumberFormat::FORMAT_DATE_TIME2 => [self::class, 'formatDateTime2'],
        NumberFormat::FORMAT_DATE_TIME3 => [self::class, 'formatDateTime3'],
        NumberFormat::FORMAT_DATE_TIME4 => [self::class, 'formatDateTime4'],
        NumberFormat::FORMAT_DATE_TIME5 => [self::class, 'formatDateTime5'],
        //NumberFormat::FORMAT_DATE_TIME6 => [self::class, 'formatDateTime6'], // FORMAT_DATE_TIME6 is identical to TIME4
        NumberFormat::FORMAT_DATE_TIME7 => [self::class, 'formatDateTime7'], // constant is probably mis-coded
        NumberFormat::FORMAT_DATE_TIME8 => [self::class, 'formatDateTime8'],
        NumberFormat::FORMAT_DATE_TIME_INTERVAL_HMS => [self::class, 'formatDateTimeIntervalHms'],
        NumberFormat::FORMAT_DATE_YYYYMMDDSLASH => [self::class, 'formatDateYyyymmddslash'],
        NumberFormat::FORMAT_DATE_LONG_DATE => [self::class, 'formatDateLongDate'],
        NumberFormat::FORMAT_CURRENCY_USD_INTEGER => [self::class, 'formatCurrencyUsdInteger'],
        NumberFormat::FORMAT_CURRENCY_USD => [self::class, 'formatCurrencyUsd'],
        NumberFormat::FORMAT_ACCOUNTING_USD => [self::class, 'formatCurrencyUsd'], // ACCOUNTING and CURRENCY are same in Ods
        NumberFormat::FORMAT_CURRENCY_EUR_INTEGER => [self::class, 'formatCurrencyEurInteger'],
        NumberFormat::FORMAT_CURRENCY_EUR => [self::class, 'formatCurrencyEur'],
        NumberFormat::FORMAT_ACCOUNTING_EUR => [self::class, 'formatCurrencyEur'], // ACCOUNTING and CURRENCY are same in Ods
        NumberFormat::FORMAT_CURRENCY_GBP_INTEGER => [self::class, 'formatCurrencyGbpInteger'],
        NumberFormat::FORMAT_CURRENCY_GBP => [self::class, 'formatCurrencyGbp'],
        NumberFormat::FORMAT_CURRENCY_YEN_YUAN_INTEGER => [self::class, 'formatCurrencyYenYuanInteger'],
        NumberFormat::FORMAT_CURRENCY_YEN_YUAN => [self::class, 'formatCurrencyYenYuan'],
    ];

    protected static function formatNumber(self $obj, string $name): void
    {
        $obj->writer->startElement('number:number-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->endElement(); // number:number
        $obj->writer->endElement(); // number:number-style
    }

    protected static function formatNumber0(self $obj, string $name): void
    {
        $obj->writer->startElement('number:number-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $obj->writer->writeAttribute('number:decimal-places', '1');
        $obj->writer->writeAttribute('number:min-decimal-places', '1');
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->endElement(); // number:number
        $obj->writer->endElement(); // number:number-style
    }

    protected static function formatNumber00(self $obj, string $name): void
    {
        $obj->writer->startElement('number:number-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $obj->writer->writeAttribute('number:decimal-places', '2');
        $obj->writer->writeAttribute('number:min-decimal-places', '2');
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->endElement(); // number:number
        $obj->writer->endElement(); // number:number-style
    }

    protected static function formatNumberCommaSeparated1(self $obj, string $name): void
    {
        $obj->writer->startElement('number:number-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $obj->writer->writeAttribute('number:decimal-places', '2');
        $obj->writer->writeAttribute('number:min-decimal-places', '2');
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->writeAttribute('number:grouping', 'true');
        $obj->writer->endElement(); // number:number
        $obj->writer->endElement(); // number:number-style
    }

    protected static function formatNumberCommaSeparated2(self $obj, string $name): void
    {
        $obj->writer->startElement('number:number-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $obj->writer->writeAttribute('number:decimal-places', '2');
        $obj->writer->writeAttribute('number:min-decimal-places', '2');
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->writeAttribute('number:grouping', 'true');
        $obj->writer->endElement(); // number:number
        $obj->writer->startElement('number:text');
        //$obj->writer->writeAttribute('loext:blank-width-char', '-');
        $obj->writer->text(' ');
        $obj->writer->endElement(); // number:text
        $obj->writer->endElement(); // number:number-style
    }

    protected static function formatPercentage(self $obj, string $name): void
    {
        $obj->writer->startElement('number:percentage-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $obj->writer->writeAttribute('number:decimal-places', '0');
        $obj->writer->writeAttribute('number:min-decimal-places', '0');
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->endElement(); // number:number
        $obj->writer->writeElement('number:text', '%');
        $obj->writer->endElement(); // number:percentage-style
    }

    protected static function formatPercentage0(self $obj, string $name): void
    {
        $obj->writer->startElement('number:percentage-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $obj->writer->writeAttribute('number:decimal-places', '1');
        $obj->writer->writeAttribute('number:min-decimal-places', '1');
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->endElement(); // number:number
        $obj->writer->writeElement('number:text', '%');
        $obj->writer->endElement(); // number:percentage-style
    }

    protected static function formatPercentage00(self $obj, string $name): void
    {
        $obj->writer->startElement('number:percentage-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $obj->writer->writeAttribute('number:decimal-places', '2');
        $obj->writer->writeAttribute('number:min-decimal-places', '2');
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->endElement(); // number:number
        $obj->writer->writeElement('number:text', '%');
        $obj->writer->endElement(); // number:percentage-style
    }

    protected static function formatDateYyyymmdd(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:year');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:year
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:day');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateDdmmyyyy(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:day');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:year');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:year
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateDmyslash(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:day');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->writeElement('number:month');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->writeElement('number:year');
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateDmyminus(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:day');
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->writeElement('number:month');
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->writeElement('number:year');
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateDmminus(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:day');
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->writeElement('number:month');
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateMyminus(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:month');
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->writeElement('number:year');
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateXlsx14(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:day');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->writeElement('number:year');
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateXlsx14Actual(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:month');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->writeElement('number:day');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->startElement('number:year');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:year
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateXlsx15(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:day');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:textual', 'true');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->writeElement('number:year');
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateXlsx15Yyyy(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:day');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:textual', 'true');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:year');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:year
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateXlsx16(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:day');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:textual', 'true');
        $obj->writer->endElement(); // number:month
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateXlsx17(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:textual', 'true');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->writeElement('number:year');
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateXlsx22(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:month');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->writeElement('number:day');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->startElement('number:year');
        $obj->writer->endElement(); // number:year
        $obj->writer->writeElement('number:text', ' ');
        $obj->writer->writeElement('number:hours');
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateXlsx22Actual(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:month');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->writeElement('number:day');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->startElement('number:year');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:year
        $obj->writer->writeElement('number:text', ' ');
        $obj->writer->writeElement('number:hours');
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateDatetime(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:day');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->writeElement('number:month');
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->startElement('number:year');
        $obj->writer->endElement(); // number:year
        $obj->writer->writeElement('number:text', ' ');
        $obj->writer->writeElement('number:hours');
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateDatetimeBetter(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:year');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:year
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', '-');
        $obj->writer->startElement('number:day');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day
        $obj->writer->writeElement('number:text', ' ');
        $obj->writer->startElement('number:hours');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:hours
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatDateTime1(self $obj, string $name): void
    {
        $obj->writer->startElement('number:time-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:hours');
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->writeElement('number:text', ' ');
        $obj->writer->writeElement('number:am-pm');
        $obj->writer->endElement(); // number:time-style
    }

    protected static function formatDateTime2(self $obj, string $name): void
    {
        $obj->writer->startElement('number:time-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:hours');
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:seconds');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:seconds
        $obj->writer->writeElement('number:text', ' ');
        $obj->writer->writeElement('number:am-pm');
        $obj->writer->endElement(); // number:time-style
    }

    protected static function formatDateTime3(self $obj, string $name): void
    {
        $obj->writer->startElement('number:time-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:hours');
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->endElement(); // number:time-style
    }

    protected static function formatDateTime4(self $obj, string $name): void
    {
        // TIME4 and TIME6 are identical
        $obj->writer->startElement('number:time-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:hours');
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:seconds');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:seconds
        $obj->writer->endElement(); // number:time-style
    }

    protected static function formatDateTime5(self $obj, string $name): void
    {
        $obj->writer->startElement('number:time-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:seconds');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:seconds
        $obj->writer->endElement(); // number:time-style
    }

    protected static function formatDateTime7(self $obj, string $name): void
    {
        // constant is probably mis-coded
        $obj->writer->startElement('number:time-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:seconds');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:seconds
        $obj->writer->endElement(); // number:time-style
    }

    protected static function formatDateTime8(self $obj, string $name): void
    {
        $obj->writer->startElement('number:time-style');
        $obj->writer->writeAttribute('style:name', $name . 'P0');
        $obj->writer->writeElement('number:hours');
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:seconds');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:seconds
        $obj->writer->endElement(); // number:time-style
        $obj->writer->startElement('number:text-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:text-content');
        $obj->writer->startElement('style:map');
        $obj->writer->writeAttribute('style:condition', 'value()>=0');
        $obj->writer->writeAttribute('style:apply-style-name', $name . 'P0');
        $obj->writer->endElement(); // number:style-map
        $obj->writer->endElement(); // number:text-style
    }

    protected static function formatDateTimeIntervalHms(self $obj, string $name): void
    {
        $obj->writer->startElement('number:time-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeAttribute(
            'number:truncate-on-overflow',
            'false'
        );
        $obj->writer->startElement('number:hours');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:hours
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:minutes');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:minutes
        $obj->writer->writeElement('number:text', ':');
        $obj->writer->startElement('number:seconds');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:seconds
        $obj->writer->endElement(); // number:time-style
    }

    protected static function formatDateYyyymmddslash(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name . 'P0');
        $obj->writer->startElement('number:year');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:year
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', '/');
        $obj->writer->startElement('number:day');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day
        $obj->writer->endElement(); // number:date-style
        $obj->writer->startElement('number:text-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->writeElement('number:text-content');
        $obj->writer->startElement('style:map');
        $obj->writer->writeAttribute('style:condition', 'value()>=0');
        $obj->writer->writeAttribute('style:apply-style-name', $name . 'P0');
        $obj->writer->endElement(); // number:style-map
        $obj->writer->endElement(); // number:text-style
    }

    protected static function formatDateLongDate(self $obj, string $name): void
    {
        $obj->writer->startElement('number:date-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:day-of-week');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:day-of-week
        $obj->writer->writeElement('number:text', ', ');
        $obj->writer->startElement('number:month');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->writeAttribute('number:textual', 'true');
        $obj->writer->endElement(); // number:month
        $obj->writer->writeElement('number:text', ' ');
        $obj->writer->writeElement('number:day');
        $obj->writer->writeElement('number:text', ', ');
        $obj->writer->startElement('number:year');
        $obj->writer->writeAttribute('number:style', 'long');
        $obj->writer->endElement(); // number:year
        $obj->writer->endElement(); // number:date-style
    }

    protected static function formatCurrencyUsdInteger(self $obj, string $name, string $symbol = '$'): void
    {
        $obj->writer->startElement('number:number-style'); // not currency-style
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->WriteElement('number:text', $symbol);
        $obj->writer->startElement('number:number');
        $decimals = '0';
        $obj->writer->writeAttribute('number:decimal-places', $decimals);
        $obj->writer->writeAttribute('number:min-decimal-places', $decimals);
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->writeAttribute('number:grouping', 'true');
        $obj->writer->startElement('number:embedded-text');
        $obj->writer->writeAttribute('number-position', '0');
        $obj->writer->text(' ');
        $obj->writer->endElement(); // number:embedded-text
        $obj->writer->endElement(); // number:number
        $obj->writer->endElement(); // number:number-style
    }

    protected static function formatCurrencyGbpInteger(self $obj, string $name): void
    {
        self::formatCurrencyUsdInteger($obj, $name, '£');
    }

    protected static function formatCurrencyYenYuanInteger(self $obj, string $name): void
    {
        self::formatCurrencyUsdInteger($obj, $name, '￥');
    }

    protected static function formatCurrencyUsd(self $obj, string $name, string $symbol = '$'): void
    {
        // Ods uses same format for Currency and Accounting
        $obj->writer->startElement('number:number-style'); // NOT currency-style
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->WriteElement('number:text', $symbol);
        $obj->writer->startElement('number:number');
        $decimals = '2';
        $obj->writer->writeAttribute('number:decimal-places', $decimals);
        $obj->writer->writeAttribute('number:min-decimal-places', $decimals);
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->writeAttribute('number:grouping', 'true');
        $obj->writer->endElement(); // number:number
        $obj->writer->writeElement('number:text', ' ');
        $obj->writer->endElement(); // number:currency-style
    }

    protected static function formatCurrencyGbp(self $obj, string $name): void
    {
        self::formatCurrencyUsd($obj, $name, '£');
    }

    protected static function formatCurrencyYenYuan(self $obj, string $name): void
    {
        self::formatCurrencyUsd($obj, $name, '￥');
    }

    protected static function formatCurrencyEurInteger(self $obj, string $name): void
    {
        $obj->writer->startElement('number:currency-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $decimals = '0';
        $obj->writer->writeAttribute('number:decimal-places', $decimals);
        $obj->writer->writeAttribute('number:min-decimal-places', $decimals);
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->writeAttribute('number:grouping', 'true');
        $obj->writer->startElement('number:embedded-text');
        $obj->writer->writeAttribute('number:position', '0');
        $obj->writer->endElement(); // number:embedded-text
        $obj->writer->endElement(); // number:number
        $obj->writer->startElement('number:text');
        // $obj->writer->writeAttribute('loext:blank-width-char', '-');
        $obj->writer->text(' ');
        $obj->writer->endElement(); // number:text
        $obj->writer->startElement('number:currency-symbol');
        $obj->writer->writeAttribute('number:language', 'en');
        $obj->writer->writeAttribute('number:country', 'us');
        $obj->writer->text('€');
        $obj->writer->endElement(); // number:currency-symbol

        $obj->writer->endElement(); // number:currency-style
    }

    protected static function formatCurrencyEur(self $obj, string $name): void
    {
        // Ods uses same format for Currency and Accounting
        $obj->writer->startElement('number:currency-style');
        $obj->writer->writeAttribute('style:name', $name);
        $obj->writer->startElement('number:number');
        $decimals = '2';
        $obj->writer->writeAttribute('number:decimal-places', $decimals);
        $obj->writer->writeAttribute('number:min-decimal-places', $decimals);
        $obj->writer->writeAttribute('number:min-integer-digits', '1');
        $obj->writer->writeAttribute('number:grouping', 'true');
        $obj->writer->endElement(); // number:number
        $obj->writer->startElement('number:text');
        // $obj->writer->writeAttribute('loext:blank-width-char', '-');
        $obj->writer->text(' ');
        $obj->writer->endElement(); // number:text
        $obj->writer->startElement('number:currency-symbol');
        $obj->writer->writeAttribute('number:language', 'en');
        $obj->writer->writeAttribute('number:country', 'us');
        $obj->writer->text('€');
        $obj->writer->endElement(); // number:currency-symbol

        $obj->writer->endElement(); // number:currency-style
    }
}
