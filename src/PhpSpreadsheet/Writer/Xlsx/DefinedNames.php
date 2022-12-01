<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet as ActualWorksheet;

class DefinedNames
{
    /** @var XMLWriter */
    private $objWriter;

    /** @var Spreadsheet */
    private $spreadsheet;

    public function __construct(XMLWriter $objWriter, Spreadsheet $spreadsheet)
    {
        $this->objWriter = $objWriter;
        $this->spreadsheet = $spreadsheet;
    }

    public function write(): void
    {
        // Write defined names
        $this->objWriter->startElement('definedNames');

        // Named ranges
        if (count($this->spreadsheet->getDefinedNames()) > 0) {
            // Named ranges
            $this->writeNamedRangesAndFormulae();
        }

        // Other defined names
        $sheetCount = $this->spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            // NamedRange for autoFilter
            $this->writeNamedRangeForAutofilter($this->spreadsheet->getSheet($i), $i);

            // NamedRange for Print_Titles
            $this->writeNamedRangeForPrintTitles($this->spreadsheet->getSheet($i), $i);

            // NamedRange for Print_Area
            $this->writeNamedRangeForPrintArea($this->spreadsheet->getSheet($i), $i);
        }

        $this->objWriter->endElement();
    }

    /**
     * Write defined names.
     */
    private function writeNamedRangesAndFormulae(): void
    {
        // Loop named ranges
        $definedNames = $this->spreadsheet->getDefinedNames();
        foreach ($definedNames as $definedName) {
            $this->writeDefinedName($definedName);
        }
    }

    /**
     * Write Defined Name for named range.
     */
    private function writeDefinedName(DefinedName $definedName): void
    {
        // definedName for named range
        $local = -1;
        if ($definedName->getLocalOnly() && $definedName->getScope() !== null) {
            try {
                $local = $definedName->getScope()->getParent()->getIndex($definedName->getScope());
            } catch (Exception $e) {
                // See issue 2266 - deleting sheet which contains
                //     defined names will cause Exception above.
                return;
            }
        }
        $this->objWriter->startElement('definedName');
        $this->objWriter->writeAttribute('name', $definedName->getName());
        if ($local >= 0) {
            $this->objWriter->writeAttribute(
                'localSheetId',
                "$local"
            );
        }

        $definedRange = $this->getDefinedRange($definedName);

        $this->objWriter->writeRawData($definedRange);

        $this->objWriter->endElement();
    }

    /**
     * Write Defined Name for autoFilter.
     */
    private function writeNamedRangeForAutofilter(ActualWorksheet $worksheet, int $worksheetId = 0): void
    {
        // NamedRange for autoFilter
        $autoFilterRange = $worksheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            $this->objWriter->startElement('definedName');
            $this->objWriter->writeAttribute('name', '_xlnm._FilterDatabase');
            $this->objWriter->writeAttribute('localSheetId', "$worksheetId");
            $this->objWriter->writeAttribute('hidden', '1');

            // Create absolute coordinate and write as raw text
            $range = Coordinate::splitRange($autoFilterRange);
            $range = $range[0];
            //    Strip any worksheet ref so we can make the cell ref absolute
            [, $range[0]] = ActualWorksheet::extractSheetTitle($range[0], true);

            $range[0] = Coordinate::absoluteCoordinate($range[0]);
            if (count($range) > 1) {
                $range[1] = Coordinate::absoluteCoordinate($range[1]);
            }
            $range = implode(':', $range);

            $this->objWriter->writeRawData('\'' . str_replace("'", "''", $worksheet->getTitle()) . '\'!' . $range);

            $this->objWriter->endElement();
        }
    }

    /**
     * Write Defined Name for PrintTitles.
     */
    private function writeNamedRangeForPrintTitles(ActualWorksheet $worksheet, int $worksheetId = 0): void
    {
        // NamedRange for PrintTitles
        if ($worksheet->getPageSetup()->isColumnsToRepeatAtLeftSet() || $worksheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
            $this->objWriter->startElement('definedName');
            $this->objWriter->writeAttribute('name', '_xlnm.Print_Titles');
            $this->objWriter->writeAttribute('localSheetId', "$worksheetId");

            // Setting string
            $settingString = '';

            // Columns to repeat
            if ($worksheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
                $repeat = $worksheet->getPageSetup()->getColumnsToRepeatAtLeft();

                $settingString .= '\'' . str_replace("'", "''", $worksheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
            }

            // Rows to repeat
            if ($worksheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
                if ($worksheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
                    $settingString .= ',';
                }

                $repeat = $worksheet->getPageSetup()->getRowsToRepeatAtTop();

                $settingString .= '\'' . str_replace("'", "''", $worksheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
            }

            $this->objWriter->writeRawData($settingString);

            $this->objWriter->endElement();
        }
    }

    /**
     * Write Defined Name for PrintTitles.
     */
    private function writeNamedRangeForPrintArea(ActualWorksheet $worksheet, int $worksheetId = 0): void
    {
        // NamedRange for PrintArea
        if ($worksheet->getPageSetup()->isPrintAreaSet()) {
            $this->objWriter->startElement('definedName');
            $this->objWriter->writeAttribute('name', '_xlnm.Print_Area');
            $this->objWriter->writeAttribute('localSheetId', "$worksheetId");

            // Print area
            $printArea = Coordinate::splitRange($worksheet->getPageSetup()->getPrintArea());

            $chunks = [];
            foreach ($printArea as $printAreaRect) {
                $printAreaRect[0] = Coordinate::absoluteReference($printAreaRect[0]);
                $printAreaRect[1] = Coordinate::absoluteReference($printAreaRect[1]);
                $chunks[] = '\'' . str_replace("'", "''", $worksheet->getTitle()) . '\'!' . implode(':', $printAreaRect);
            }

            $this->objWriter->writeRawData(implode(',', $chunks));

            $this->objWriter->endElement();
        }
    }

    private function getDefinedRange(DefinedName $definedName): string
    {
        $definedRange = $definedName->getValue();
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/mui',
            $definedRange,
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
                if (($offset === 0) || ($definedRange[$offset - 1] !== ':')) {
                    // We should have a worksheet
                    $ws = $definedName->getWorksheet();
                    $worksheet = ($ws === null) ? null : $ws->getTitle();
                }
            } else {
                $worksheet = str_replace("''", "'", trim($worksheet, "'"));
            }

            if (!empty($worksheet)) {
                $newRange = "'" . str_replace("'", "''", $worksheet) . "'!";
            }
            $newRange = "{$newRange}{$column}{$row}";

            $definedRange = substr($definedRange, 0, $offset) . $newRange . substr($definedRange, $offset + $length);
        }

        if (substr($definedRange, 0, 1) === '=') {
            $definedRange = substr($definedRange, 1);
        }

        return $definedRange;
    }
}
