<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class MissingArgumentsTest extends TestCase
{
    /**
     * @dataProvider providerMissingArguments
     */
    public function testMissingArguments(mixed $expected, string $formula): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        self::assertSame($expected, $sheet->getCell('A1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerMissingArguments(): array
    {
        return [
            'argument missing at end' => [0, '=min(3,2,)'],
            'argument missing at beginning' => [0, '=mina(,3,2)'],
            'argument missing in middle' => [0, '=min(3,,2)'],
            'missing argument is not result' => [-2, '=min(3,-2,)'],
            'max with missing argument' => [0, '=max(-3,-2,)'],
            'maxa with missing argument' => [0, '=maxa(-3,-2,)'],
            'max with null cell' => [-2, '=max(-3,-2,Z1)'],
            'min with null cell' => [2, '=min(3,2,Z1)'],
            'product ignores null argument' => [6.0, '=product(3,2,)'],
            'embedded function' => [5, '=sum(3,2,min(3,2,))'],
            'unaffected embedded function' => [8, '=sum(3,2,max(3,2,))'],
        ];
    }
}
