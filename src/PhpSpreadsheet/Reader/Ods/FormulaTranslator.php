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
        $excelAddress = (string) preg_replace(
            [
                '/\$?([^\.]+)\.([^\.]+):\$?([^\.]+)\.([^\.]+)/miu',
                '/\$?([^\.]+)\.([^\.]+):\.([^\.]+)/miu', // Cell range reference in another sheet
                '/\$?([^\.]+)\.([^\.]+)/miu', // Cell reference in another sheet
                '/\.([^\.]+):\.([^\.]+)/miu', // Cell range reference
                '/\.([^\.]+)/miu', // Simple cell reference
            ],
            [
                '$1!$2:$4',
                '$1!$2:$3',
                '$1!$2',
                '$1:$2',
                '$1',
            ],
            $excelAddress
        );

        return $excelAddress;
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
            $tKey = $tKey === false;
            if ($tKey) {
                $value = (string) preg_replace(
                    [
                        '/\[\$?([^\.]+)\.([^\.]+):\.([^\.]+)\]/miu', // Cell range reference in another sheet
                        '/\[\$?([^\.]+)\.([^\.]+)\]/miu', // Cell reference in another sheet
                        '/\[\.([^\.]+):\.([^\.]+)\]/miu', // Cell range reference
                        '/\[\.([^\.]+)\]/miu', // Simple cell reference
                    ],
                    [
                        '$1!$2:$3',
                        '$1!$2',
                        '$1:$2',
                        '$1',
                    ],
                    $value
                );
                // Convert references to defined names/formulae
                $value = str_replace('$$', '', $value);

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

                $value = (string) preg_replace('/COM\.MICROSOFT\./ui', '', $value);
            }
        }

        // Then rebuild the formula string
        $excelFormula = implode('"', $temp);

        return $excelFormula;
    }
}
