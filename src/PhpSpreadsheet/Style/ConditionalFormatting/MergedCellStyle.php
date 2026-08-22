<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MergedCellStyle
{
    private bool $matched = false;

    /**
     * Indicate whether the last call to getMergedStyle found
     * any conditional or table styles affecting the cell in question.
     */
    public function getMatched(): bool
    {
        return $this->matched;
    }

    /**
     * Return a style that combines the base style for a cell
     * with any conditional or table styles applicable to the cell.
     *
     * @param bool $tableFormats True/false to indicate whether
     *        custom table styles should be considered.
     *        Note that builtin table styles are not supported.
     * @param bool $conditionals True/false to indicate whether
     *        conditional styles should be considered.
     */
    public function getMergedStyle(Worksheet $worksheet, string $coordinate, bool $tableFormats = true, bool $conditionals = true, ?bool $builtInTableStyles = null): Style
    {
        $builtInTableStyles ??= $tableFormats;
        $this->matched = false;
        $styleMerger = new StyleMerger($worksheet->getStyle($coordinate));
        if ($tableFormats) {
            $this->assessTables($worksheet, $coordinate, $styleMerger);
        }
        if ($builtInTableStyles) {
            $this->assessBuiltinTables($worksheet, $coordinate, $styleMerger);
        }
        if ($conditionals) {
            $this->assessConditionals($worksheet, $coordinate, $styleMerger);
        }

        return $styleMerger->getStyle();
    }

    private function assessTables(Worksheet $worksheet, string $coordinate, StyleMerger $styleMerger): void
    {
        $tables = $worksheet->getTablesWithStylesForCell($worksheet->getCell($coordinate));
        foreach ($tables as $ts) {
            $dxfsTableStyle = $ts->getStyle()->getTableDxfsStyle();
            if ($dxfsTableStyle !== null) {
                $tableRow = $ts->getRowNumber($coordinate);
                if ($tableRow === 0 && $dxfsTableStyle->getHeaderRowStyle() !== null) {
                    $styleMerger->mergeStyle(
                        $dxfsTableStyle->getHeaderRowStyle()
                    );
                    $this->matched = true;
                } elseif ($tableRow % 2 === 1 && $dxfsTableStyle->getFirstRowStripeStyle() !== null) {
                    $styleMerger->mergeStyle(
                        $dxfsTableStyle->getFirstRowStripeStyle()
                    );
                    $this->matched = true;
                } elseif ($tableRow % 2 === 0 && $dxfsTableStyle->getSecondRowStripeStyle() !== null) {
                    $styleMerger->mergeStyle(
                        $dxfsTableStyle->getSecondRowStripeStyle()
                    );
                    $this->matched = true;
                }
            }
        }
    }

    private static ?Style $headerStyle = null;

    private static ?Style $firstRowStyle = null;

    private function assessBuiltinTables(Worksheet $worksheet, string $coordinate, StyleMerger $styleMerger): void
    {
        if (self::$headerStyle === null) {
            self::$headerStyle = new Style();
            self::$headerStyle->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getEndColor()
                ->setArgb('FF000000');
            self::$headerStyle->getFill()->getStartColor()
                ->setArgb('FF000000');
            self::$headerStyle->getFont()
                ->getColor()->setRgb('FFFFFF');
        }
        if (self::$firstRowStyle === null) {
            self::$firstRowStyle = new Style();
            self::$firstRowStyle->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getEndColor()
                ->setArgb('FFD9D9D9');
            self::$firstRowStyle->getFill()->getStartColor()
                ->setArgb('FFD9D9D9');
        }
        $tables = $worksheet->getTablesWithoutStylesForCell($worksheet->getCell($coordinate));
        foreach ($tables as $table) {
            $tableRow = $table->getRowNumber($coordinate);
            if ($tableRow === 0 && $table->getShowHeaderRow()) {
                $styleMerger->mergeStyle(self::$headerStyle);
                $this->matched = true;
            } elseif ($tableRow % 2 === 1) {
                $styleMerger->mergeStyle(self::$firstRowStyle);
                $this->matched = true;
            }
        }
    }

    private function assessConditionals(Worksheet $worksheet, string $coordinate, StyleMerger $styleMerger): void
    {
        if ($worksheet->getConditionalRange($coordinate) !== null) {
            $assessor = new CellStyleAssessor($worksheet->getCell($coordinate), $worksheet->getConditionalRange($coordinate));
        } else {
            $assessor = new CellStyleAssessor($worksheet->getCell($coordinate), $coordinate);
        }
        $matchedStyle = $assessor
            ->matchConditionsReturnNullIfNoneMatched(
                $worksheet->getConditionalStyles($coordinate),
                $worksheet->getCell($coordinate)
                    ->getCalculatedValueString(),
                true
            );
        if ($matchedStyle !== null) {
            $this->matched = true;
            $styleMerger->mergeStyle($matchedStyle);
        }
    }
}
