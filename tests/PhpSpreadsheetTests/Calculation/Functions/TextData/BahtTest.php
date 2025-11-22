<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BahtTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerBAHTTEXT')]
    public function testBAHTTEXT(mixed $expectedResult, bool|string|float|int $number): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (is_bool($number)) {
            $formulaValue = $number ? 'TRUE' : 'FALSE';
        } elseif (is_string($number)) {
            $formulaValue = '"' . $number . '"';
        } else {
            $formulaValue = (string) $number;
        }

        $sheet->getCell('A1')->setValue('=BAHTTEXT(' . $formulaValue . ')');
        $result = $sheet->getCell('A1')->getCalculatedValue();

        self::assertSame($expectedResult, $result);
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerBAHTTEXT(): array
    {
        return require 'tests/data/Calculation/TextData/BAHTTEXT.php';
    }
}
