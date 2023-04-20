<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    /**
     * @dataProvider providerINDEX
     *
     * @param mixed $expectedResult
     * @param mixed $matrix
     * @param mixed $rowNum
     * @param mixed $colNum
     */
    public function testINDEX($expectedResult, $matrix, $rowNum = null, $colNum = null): void
    {
        if ($rowNum === null) {
            $result = Matrix::index($matrix);
        } elseif ($colNum === null) {
            $result = Matrix::index($matrix, $rowNum);
        } else {
            $result = Matrix::index($matrix, $rowNum, $colNum);
        }
        self::assertEquals($expectedResult, $result);
    }

    public static function providerINDEX(): array
    {
        return require 'tests/data/Calculation/LookupRef/INDEX.php';
    }

    /**
     * @dataProvider providerIndexArray
     */
    public function testIndexArray(array $expectedResult, string $matrix, string $rows, string $columns): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=INDEX({$matrix}, {$rows}, {$columns})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIndexArray(): array
    {
        return [
            'row/column vectors' => [
                [[2, 3], [5, 6]],
                '{1, 2, 3; 4, 5, 6; 7, 8, 9}',
                '{1; 2}',
                '{2, 3}',
            ],
            'return row' => [
                [1 => [4, 5, 6]],
                '{1, 2, 3; 4, 5, 6; 7, 8, 9}',
                '2',
                '0',
            ],
            'return column' => [
                [[2], [5], [8]],
                '{1, 2, 3; 4, 5, 6; 7, 8, 9}',
                '0',
                '2',
            ],
        ];
    }

    public function testPropagateDiv0(): void
    {
        // issue 3396 error was always being treated as #VALUE!
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(0);
        $sheet->getCell('A3')->setValue(1);
        $sheet->getCell('B3')->setValue(1);
        $sheet->getCell('C3')->setValue('=1/A1');
        $sheet->getCell('D3')->setValue('=1/A1');
        $sheet->getCell('E3')->setValue('xyz');
        $sheet->getCell('A4')->setValue(false);
        $sheet->getCell('B4')->setValue(true);
        $sheet->getCell('C4')->setValue(true);
        $sheet->getCell('D4')->setValue(false);
        $sheet->getCell('E4')->setValue(false);
        $sheet->getCell('A6')->setValue('=INDEX(A3:E3/A4:E4,1,1)');
        $sheet->getCell('B6')->setValue('=INDEX(A3:E3/A4:E4,1,2)');
        $sheet->getCell('C6')->setValue('=INDEX(A3:E3/A4:E4,1,3)');
        $sheet->getCell('D6')->setValue('=INDEX(A3:E3/A4:E4,1,4)');
        $sheet->getCell('E6')->setValue('=INDEX(A3:E3/A4:E4,1,5)');

        self::assertSame('#DIV/0!', $sheet->getCell('A6')->getCalculatedValue());
        self::assertSame(1, $sheet->getCell('B6')->getCalculatedValue());
        self::assertSame('#DIV/0!', $sheet->getCell('C6')->getCalculatedValue());
        self::assertSame('#DIV/0!', $sheet->getCell('D6')->getCalculatedValue());
        self::assertSame('#VALUE!', $sheet->getCell('E6')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }
}
