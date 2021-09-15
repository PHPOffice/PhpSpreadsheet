<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NamedExpressions
{
    /** @var XMLWriter */
    private $objWriter;

    /** @var Spreadsheet */
    private $spreadsheet;

    /** @var Formula */
    private $formulaConvertor;

    public function __construct(XMLWriter $objWriter, Spreadsheet $spreadsheet, Formula $formulaConvertor)
    {
        $this->objWriter = $objWriter;
        $this->spreadsheet = $spreadsheet;
        $this->formulaConvertor = $formulaConvertor;
    }

    public function write(): string
    {
        $this->objWriter->startElement('table:named-expressions');
        $this->writeExpressions();
        $this->objWriter->endElement();

        return '';
    }

    private function writeExpressions(): void
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
        $title = ($definedName->getWorksheet() !== null) ? $definedName->getWorksheet()->getTitle() : $defaultWorksheet->getTitle();
        $this->objWriter->writeAttribute('table:name', $definedName->getName());
        $this->objWriter->writeAttribute(
            'table:expression',
            $this->formulaConvertor->convertFormula($definedName->getValue(), $title)
        );
        $this->objWriter->writeAttribute('table:base-cell-address', $this->convertAddress(
            $definedName,
            "'" . $title . "'!\$A\$1"
        ));
    }

    private function writeNamedRange(DefinedName $definedName): void
    {
        $baseCell = '$A$1';
        $ws = $definedName->getWorksheet();
        if ($ws !== null) {
            $baseCell = "'" . $ws->getTitle() . "'!$baseCell";
        }
        $this->objWriter->writeAttribute('table:name', $definedName->getName());
        $this->objWriter->writeAttribute('table:base-cell-address', $this->convertAddress(
            $definedName,
            $baseCell
        ));
        $this->objWriter->writeAttribute('table:cell-range-address', $this->convertAddress($definedName, $definedName->getValue()));
    }

    private function convertAddress(DefinedName $definedName, string $address): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/mui',
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
                    $ws = $definedName->getWorksheet();
                    if ($ws !== null) {
                        $worksheet = $ws->getTitle();
                    }
                }
            } else {
                $worksheet = str_replace("''", "'", trim($worksheet, "'"));
            }
            if (!empty($worksheet)) {
                $newRange = "'" . str_replace("'", "''", $worksheet) . "'.";
            }

            if (!empty($column)) {
                $newRange .= $column;
            }
            if (!empty($row)) {
                $newRange .= $row;
            }

            $address = substr($address, 0, $offset) . $newRange . substr($address, $offset + $length);
        }

        if (substr($address, 0, 1) === '=') {
            $address = substr($address, 1);
        }

        return $address;
    }
}
