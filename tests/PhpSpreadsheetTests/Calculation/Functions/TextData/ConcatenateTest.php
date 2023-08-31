<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

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

    public static function providerCONCATENATE(): array
    {
        return require 'tests/data/Calculation/TextData/CONCATENATE.php';
    }

    public function testConcatenateWithIndexMatch(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Formula');
        $sheet1->fromArray(
            [
                ['Number', 'Formula'],
                [52101293, '=CONCAT(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)))'],
            ]
        );
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Lookup');
        $sheet2->fromArray(
            [
                ['Lookup', 'Match'],
                [52101293, 'PHP'],
            ]
        );
        self::assertSame('PHP', $sheet1->getCell('B2')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
