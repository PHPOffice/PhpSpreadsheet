<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FormulaTranslator
{
    public static function convertToExcelAddressValue(string $openOfficeAddress): string
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

    public static function convertToExcelFormulaValue(string $openOfficeFormula): string
    {
        $temp = explode(Calculation::FORMULA_STRING_QUOTE, $openOfficeFormula);
        $tKey = false;
        $inMatrixBracesLevel = 0;
        $inFunctionBracesLevel = 0;
        foreach ($temp as &$value) {
            // @var string $value
            // Only replace in alternate array entries (i.e. non-quoted blocks)
            //      so that conversion isn't done in string values
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

                // Convert ODS function argument separators to Excel function argument separators
                $value = Calculation::translateSeparator(';', ',', $value, $inFunctionBracesLevel);

                // Convert ODS matrix separators to Excel matrix separators
                $value = Calculation::translateSeparator(
                    ';',
                    ',',
                    $value,
                    $inMatrixBracesLevel,
                    Calculation::FORMULA_OPEN_MATRIX_BRACE,
                    Calculation::FORMULA_CLOSE_MATRIX_BRACE
                );
                $value = Calculation::translateSeparator(
                    '|',
                    ';',
                    $value,
                    $inMatrixBracesLevel,
                    Calculation::FORMULA_OPEN_MATRIX_BRACE,
                    Calculation::FORMULA_CLOSE_MATRIX_BRACE
                );

                $value = preg_replace('/COM\.MICROSOFT\./ui', '', $value);
            }
        }

        // Then rebuild the formula string
        $excelFormula = implode('"', $temp);

        return $excelFormula;
    }
}
