<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

class IndexOnSpreadsheetTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINDEXonSpreadsheet
     *
     * @param mixed $expectedResult
     * @param null|int|string $rowNum
     * @param null|int|string $colNum
     */
    public function testIndexOnSpreadsheet($expectedResult, array $matrix, $rowNum = null, $colNum = null): void
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
}
