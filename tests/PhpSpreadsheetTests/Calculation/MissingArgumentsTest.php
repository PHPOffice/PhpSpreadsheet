<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
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
        $sheet->getCell('B1')->setValue(1);

        try {
            self::assertSame($expected, $sheet->getCell('A1')->getCalculatedValue());
        } catch (CalcExp $e) {
            self::assertSame('exception', $expected);
        }

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
            'if true missing at end' => [0, '=if(b1=1,min(3,2,),product(3,2,))'],
            'if false missing at end' => [6.0, '=if(b1=2,min(3,2,),product(3,2,))'],
            'if true missing in middle' => [0, '=if(b1=1,min(3,,2),product(3,,2))'],
            'if false missing in middle' => [6.0, '=if(b1=2,min(3,,2),product(3,,2))'],
            'if true missing at beginning' => [0, '=if(b1=1,min(,3,2),product(,3,2))'],
            'if false missing at beginning' => [6.0, '=if(b1=2,min(,3,2),product(,3,2))'],
            'if true nothing missing' => [2, '=if(b1=1,min(3,2),product(3,2))'],
            'if false nothing missing' => [6.0, '=if(b1=2,min(3,2),product(3,2))'],
            'if true empty arg' => [0, '=if(b1=1,)'],
            'if true omitted args' => ['exception', '=if(b1=1)'],
            'if true missing arg' => [0, '=if(b1=1,,6)'],
            'if false missing arg' => [0, '=if(b1=2,6,)'],
            'if false omitted arg' => [false, '=if(b1=2,6)'],
            'multiple ifs and omissions' => [0, '=IF(0<9,,IF(0=0,,1))'],
        ];
    }
}
