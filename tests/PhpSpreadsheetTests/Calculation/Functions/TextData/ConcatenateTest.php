<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

class ConcatenateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCONCATENATE
     *
     * @param mixed $expectedResult
     * @param array $args
     */
    public function testCONCATENATE($expectedResult, ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $finalArg = '';
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            $this->setCell("A$row", $arg);
            $finalArg = "A1:A$row";
        }
        $this->setCell('B1', "=CONCAT($finalArg)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerCONCATENATE(): array
    {
        return require 'tests/data/Calculation/TextData/CONCATENATE.php';
    }
}
