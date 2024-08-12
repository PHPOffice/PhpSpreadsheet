<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ConcatenateTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCONCATENATE
     */
    public function testCONCATENATE(mixed $expectedResult, mixed ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $finalArg = '';
        $comma = '';
        foreach ($args as $arg) {
            $finalArg .= $comma;
            $comma = ',';
            if (is_bool($arg)) {
                $finalArg .= $arg ? 'true' : 'false';
            } elseif ($arg === 'A2') {
                $finalArg .= 'A2';
                $sheet->getCell('A2')->setValue('=2/0');
            } elseif ($arg === 'A3') {
                $finalArg .= 'A3';
                $sheet->getCell('A3')->setValue(str_repeat('Ԁ', DataType::MAX_STRING_LENGTH - 5));
            } else {
                $finalArg .= '"' . (string) $arg . '"';
            }
        }
        $this->setCell('B1', "=CONCATENATE($finalArg)");
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
                [52101293, '=CONCATENATE(INDEX(Lookup!B2, MATCH(A2, Lookup!A2, 0)))'],
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
