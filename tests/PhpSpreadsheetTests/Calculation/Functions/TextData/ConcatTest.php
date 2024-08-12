<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ConcatTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCONCAT
     */
    public function testCONCAT(mixed $expectedResult, mixed ...$args): void
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

    public static function providerCONCAT(): array
    {
        return require 'tests/data/Calculation/TextData/CONCAT.php';
    }

    public function testConcatWithIndexMatch(): void
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
