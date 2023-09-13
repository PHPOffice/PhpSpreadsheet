<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class LookupTest extends TestCase
{
    /**
     * @dataProvider providerLOOKUP
     */
    public function testLOOKUP(mixed $expectedResult, mixed ...$args): void
    {
        $result = LookupRef\Lookup::lookup(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLOOKUP(): array
    {
        return require 'tests/data/Calculation/LookupRef/LOOKUP.php';
    }

    /**
     * @dataProvider providerLookupArray
     */
    public function testLookupArray(array $expectedResult, string $values, string $lookup, string $return): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOOKUP({$values}, {$lookup}, {$return})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLookupArray(): array
    {
        return [
            'row vector' => [
                [['Orange', 'Green', 'Red']],
                '{4.19, 5.77, 4.14}',
                '{4.14; 4.19; 5.17; 5.77; 6.39}',
                '{"Red"; "Orange"; "Yellow"; "Green"; "Blue"}',
            ],
        ];
    }

    public function testBoolsAsInt(): void
    {
        // issue 3396 not handling math operation for bool in array
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A59')->setValue('start');
        $sheet->getCell('B59')->setValue('end');
        $sheet->getCell('C59')->setValue('percent');
        $sheet->getCell('A60')->setValue('=DATEVALUE("1950-01-01")');
        $sheet->getCell('B60')->setValue('=DATEVALUE("2016-06-03")');
        $sheet->getCell('C60')->setValue(0.05);
        $sheet->getCell('A61')->setValue('=DATEVALUE("2016-06-04")');
        $sheet->getCell('B61')->setValue('=DATEVALUE("2021-01-05")');
        $sheet->getCell('C61')->setValue(0.08);
        $sheet->getCell('A62')->setValue('=DATEVALUE("2021-01-16")');
        $sheet->getCell('B62')->setValue('=DATEVALUE("2022-04-08")');
        $sheet->getCell('C62')->setValue(0.03);
        $sheet->getCell('A63')->setValue('=DATEVALUE("2022-04-09")');
        $sheet->getCell('B63')->setValue('=DATEVALUE("2500-12-31")');
        $sheet->getCell('C63')->setValue(0.04);

        $sheet->getCell('D5')->setValue(5);
        $sheet->getCell('E5')->setValue('=DATEVALUE("2023-01-01")');
        $sheet->getCell('D7')->setValue('=IF(E5<>"",LOOKUP(2,1/($A$60:$A$63<=E5)/($B$60:$B$63>=E5),$C$60:$C$63)*D5,"")');

        self::assertSame(0.2, $sheet->getCell('D7')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
