<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

class IndexOnSpreadsheetTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINDEXonSpreadsheet
     */
    public function testIndexOnSpreadsheet(mixed $expectedResult, array $matrix, null|int|string $rowNum = null, null|int|string $colNum = null): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->fromArray($matrix);
        $maxRow = $sheet->getHighestRow();
        $maxColumn = $sheet->getHighestColumn();
        $formulaArray = "A1:$maxColumn$maxRow";
        if ($rowNum === null) {
            $formula = "=INDEX($formulaArray)";
        } elseif ($colNum === null) {
            $formula = "=INDEX($formulaArray, $rowNum)";
        } else {
            $formula = "=INDEX($formulaArray, $rowNum, $colNum)";
        }
        $sheet->getCell('ZZ98')->setValue('x');
        $sheet->getCell('ZZ99')->setValue($formula);
        $result = $sheet->getCell('ZZ99')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerINDEXonSpreadsheet(): array
    {
        return require 'tests/data/Calculation/LookupRef/INDEXonSpreadsheet.php';
    }

    /**
     * @dataProvider providerIndexLiteralArrays
     */
    public function testLiteralArrays(mixed $expectedResult, string $indexArgs): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A10')->setValue(10);
        $sheet->getCell('B10')->setValue(11);
        $sheet->getCell('C10')->setValue(12);
        $sheet->getCell('D10')->setValue(13);
        $sheet->getCell('X10')->setValue(10);
        $sheet->getCell('X11')->setValue(11);
        $sheet->getCell('X12')->setValue(12);
        $sheet->getCell('X13')->setValue(13);
        $sheet->getCell('A1')->setValue("=INDEX($indexArgs)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIndexLiteralArrays(): array
    {
        return [
            'issue 64' => ['Fourth', '{"First","Second","Third","Fourth","Fifth","Sixth","Seventh"}, 4'],
            'issue 64 selecting first "row"' => ['First', '{"First","Second","Third","Fourth","Fifth","Sixth","Seventh"}, 1'],
            'array result condensed to single value' => [40, '{10,11;20,21;30,31;40,41;50,51;60,61},4'],
            'both row and column' => [41, '{10,11;20,21;30,31;40,41;50,51;60,61},4,2'],
            '1*1 array' => ['first', '{"first"},1'],
            'array expressed in rows' => [20, '{10;20;30;40},2'],
            'spreadsheet single row' => [11, 'A10:D10,2'],
            'spreadsheet single column' => [13, 'X10:X13,4'],
        ];
    }
}
