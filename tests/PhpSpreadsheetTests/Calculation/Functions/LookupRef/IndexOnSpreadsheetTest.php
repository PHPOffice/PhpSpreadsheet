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
}
