<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NamedExpressions
{
    private $objWriter;

    private $spreadsheet;

    public function __construct(XMLWriter $objWriter, Spreadsheet $spreadsheet)
    {
        $this->objWriter = $objWriter;
        $this->spreadsheet = $spreadsheet;
    }

    public function write(): void
    {
        $this->objWriter->startElement('table:named-expressions');
        $this->writeExpressions();
        $this->objWriter->endElement();
    }

    private function writeExpressions()
    {
        $definedNames = $this->spreadsheet->getDefinedNames();

        foreach ($definedNames as $definedName) {
            if ($definedName->isFormula()) {
                $this->objWriter->startElement('table:named-expression');
                $this->writeNamedFormula($definedName, $this->spreadsheet->getActiveSheet());
            } else {
                $this->objWriter->startElement('table:named-range');
                $this->writeNamedRange($definedName);
            }

            $this->objWriter->endElement();
        }
    }

    private function writeNamedFormula(DefinedName $definedName, Worksheet $defaultWorksheet): void
    {
        $this->objWriter->writeAttribute('table:name', $definedName->getName());
        $this->objWriter->writeAttribute('table:expression', $this->convertFormula($definedName, $definedName->getValue()));
        $this->objWriter->writeAttribute('table:base-cell-address', $this->convertAddress(
            $definedName,
            "'" . (($definedName->getWorksheet() !== null) ? $definedName->getWorksheet()->getTitle() : $defaultWorksheet->getTitle()) . "'!\$A\$1'"
        ));
    }

    private function writeNamedRange(DefinedName $definedName): void
    {
        $this->objWriter->writeAttribute('table:name', $definedName->getName());
        $this->objWriter->writeAttribute('table:base-cell-address', $this->convertAddress(
            $definedName,
            "'" . $definedName->getWorksheet()->getTitle() . "'!\$A\$1"
        ));
        $this->objWriter->writeAttribute('table:cell-range-address', $this->convertAddress($definedName, $definedName->getValue()));
    }

    private function convertAddress(DefinedName $definedName, string $address): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF . '/mui',
            $address,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $lengths = array_map('strlen', array_column($splitRanges[0], 0));
        $offsets = array_column($splitRanges[0], 1);

        $worksheets = $splitRanges[2];
        $columns = $splitRanges[6];
        $rows = $splitRanges[7];

        while ($splitCount > 0) {
            --$splitCount;
            $length = $lengths[$splitCount];
            $offset = $offsets[$splitCount];
            $worksheet = $worksheets[$splitCount][0];
            $column = $columns[$splitCount][0];
            $row = $rows[$splitCount][0];

            $newRange = '';
            if (empty($worksheet)) {
                if (($offset === 0) || ($address[$offset - 1] !== ':')) {
                    // We need a worksheet
                    $worksheet = $definedName->getWorksheet()->getTitle();
                }
            } else {
                $worksheet = str_replace("''", "'", trim($worksheet, "'"));
            }
            if (!empty($worksheet)) {
                $newRange = "\$'" . str_replace("'", "''", $worksheet) . "'.";
            }

            if (!empty($column)) {
                $newRange .= "\${$column}";
            }
            if (!empty($row)) {
                $newRange .= "\${$row}";
            }

            $address = substr($address, 0, $offset) . $newRange . substr($address, $offset + $length);
        }

        return $address;
    }

    private function convertFormula(DefinedName $definedName, string $formula): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF . '/mui',
            $formula,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $lengths = array_map('strlen', array_column($splitRanges[0], 0));
        $offsets = array_column($splitRanges[0], 1);

        $worksheets = $splitRanges[2];
        $columns = $splitRanges[6];
        $rows = $splitRanges[7];

        // Replace any commas in the formula with semi-colons for Ods
        // If by chance there are commas in worksheet names, then they will be "fixed" again in the loop
        //    because we've already extracted worksheet names with our preg_match_all()
        $formula = str_replace(',', ';', $formula);
        while ($splitCount > 0) {
            --$splitCount;
            $length = $lengths[$splitCount];
            $offset = $offsets[$splitCount];
            $worksheet = $worksheets[$splitCount][0];
            $column = $columns[$splitCount][0];
            $row = $rows[$splitCount][0];

            $newRange = '';
            if (empty($worksheet)) {
                if (($offset === 0) || ($formula[$offset - 1] !== ':')) {
                    // We need a worksheet
                    $worksheet = $definedName->getWorksheet()->getTitle();
                }
            } else {
                $worksheet = str_replace("''", "'", trim($worksheet, "'"));
            }
            if (!empty($worksheet)) {
                $newRange = "[\$'" . str_replace("'", "''", $worksheet) . "'.";
            }

            if (!empty($column)) {
                $newRange .= "\${$column}";
            }
            if (!empty($row)) {
                $newRange .= "\${$row}";
            }
            // close the wrapping [] unless this is the first part of a range
            $newRange .= substr($formula, $offset + $length, 1) !== ':' ? ']' : '';

            $formula = substr($formula, 0, $offset) . $newRange . substr($formula, $offset + $length);
        }

        return $formula;
    }
}
