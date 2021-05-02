<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use DOMElement;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

abstract class BaseReader
{
    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var string
     */
    protected $tableNs;

    public function __construct(Spreadsheet $spreadsheet, string $tableNs)
    {
        $this->spreadsheet = $spreadsheet;
        $this->tableNs = $tableNs;
    }

    abstract public function read(DOMElement $workbookData): void;

    protected function convertToExcelAddressValue(string $openOfficeAddress): string
    {
        $excelAddress = $openOfficeAddress;

        // Cell range 3-d reference
        // As we don't support 3-d ranges, we're just going to take a quick and dirty approach
        //  and assume that the second worksheet reference is the same as the first
        $excelAddress = preg_replace('/\$?([^\.]+)\.([^\.]+):\$?([^\.]+)\.([^\.]+)/miu', '$1!$2:$4', $excelAddress);
        // Cell range reference in another sheet
        $excelAddress = preg_replace('/\$?([^\.]+)\.([^\.]+):\.([^\.]+)/miu', '$1!$2:$3', $excelAddress ?? '');
        // Cell reference in another sheet
        $excelAddress = preg_replace('/\$?([^\.]+)\.([^\.]+)/miu', '$1!$2', $excelAddress ?? '');
        // Cell range reference
        $excelAddress = preg_replace('/\.([^\.]+):\.([^\.]+)/miu', '$1:$2', $excelAddress ?? '');
        // Simple cell reference
        $excelAddress = preg_replace('/\.([^\.]+)/miu', '$1', $excelAddress ?? '');

        return $excelAddress ?? '';
    }

    protected function convertToExcelFormulaValue(string $openOfficeFormula): string
    {
        $temp = explode('"', $openOfficeFormula);
        $tKey = false;
        foreach ($temp as &$value) {
            // @var string $value
            // Only replace in alternate array entries (i.e. non-quoted blocks)
            if ($tKey = !$tKey) {
                // Cell range reference in another sheet
                $value = preg_replace('/\[\$?([^\.]+)\.([^\.]+):\.([^\.]+)\]/miu', '$1!$2:$3', $value);
                // Cell reference in another sheet
                $value = preg_replace('/\[\$?([^\.]+)\.([^\.]+)\]/miu', '$1!$2', $value ?? '');
                // Cell range reference
                $value = preg_replace('/\[\.([^\.]+):\.([^\.]+)\]/miu', '$1:$2', $value ?? '');
                // Simple cell reference
                $value = preg_replace('/\[\.([^\.]+)\]/miu', '$1', $value ?? '');
                // Convert references to defined names/formulae
                $value = str_replace('$$', '', $value ?? '');

                $value = Calculation::translateSeparator(';', ',', $value, $inBraces);
            }
        }

        // Then rebuild the formula string
        $excelFormula = implode('"', $temp);

        return $excelFormula;
    }
}
