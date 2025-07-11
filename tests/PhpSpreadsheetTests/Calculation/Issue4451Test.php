<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class Issue4451Test extends TestCase
{
    public static function testReflect(): void
    {
        // Sample matrices to test with
        $matrix1 = [[1], [3]];
        $matrix2 = [[5], [8], [11]];

        // Use reflection to make the protected method accessible
        $calculation = new Calculation();
        $reflectionMethod = new ReflectionMethod(Calculation::class, 'resizeMatricesExtend');
        $reflectionMethod->setAccessible(true);

        // Call the method using reflection
        $reflectionMethod->invokeArgs($calculation, [&$matrix1, &$matrix2, count($matrix1), 1, count($matrix2), 1]);

        self::assertSame([[1], [3], [null]], $matrix1); //* @phpstan-ignore-line
    }

    /**
     * These 2 tests are contrived. They prove that method
     * works as desired, but Excel will actually return
     * a CALC error, a result I don't know how to duplicate.
     */
    public static function testExtendFirstColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');
        $calculationEngine = Calculation::getInstance($spreadsheet);
        $calculationEngine->setInstanceArrayReturnType(
            Calculation::RETURN_ARRAY_AS_ARRAY
        );

        $sheet->getCell('D5')->setValue(5);
        $sheet->getCell('E5')->setValue(20);
        $sheet->fromArray(
            [
                [5, 20, 'Apples'],
                [10, 20, 'Bananas'],
                [5, 20, 'Cherries'],
                [5, 40, 'Grapes'],
                [25, 50, 'Peaches'],
                [30, 60, 'Pears'],
                [35, 70, 'Papayas'],
                [40, 80, 'Mangos'],
                [null, 20, 'Unknown'],
            ],
            null,
            'K1',
            true
        );
        $kRows = $sheet->getHighestDataRow('K');
        self::assertSame(8, $kRows);
        $lRows = $sheet->getHighestDataRow('L');
        self::assertSame(9, $lRows);
        $mRows = $sheet->getHighestDataRow('M');
        self::assertSame(9, $mRows);
        $sheet->getCell('A1')
            ->setValue(
                "=FILTER(Products!M1:M$mRows,"
                    . "(Products!K1:K$kRows=D5)"
                    . "*(Products!L1:L$lRows=E5))"
            );

        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertSame([['Apples'], ['Cherries']], $result);
        $spreadsheet->disconnectWorksheets();
    }

    public static function testExtendSecondColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');
        $calculationEngine = Calculation::getInstance($spreadsheet);
        $calculationEngine->setInstanceArrayReturnType(
            Calculation::RETURN_ARRAY_AS_ARRAY
        );

        $sheet->getCell('D5')->setValue(5);
        $sheet->getCell('E5')->setValue(20);
        $sheet->fromArray(
            [
                [5, 20, 'Apples'],
                [10, 20, 'Bananas'],
                [5, 20, 'Cherries'],
                [5, 40, 'Grapes'],
                [25, 50, 'Peaches'],
                [30, 60, 'Pears'],
                [35, 70, 'Papayas'],
                [40, 80, 'Mangos'],
                [null, 20, 'Unknown'],
            ],
            null,
            'K1',
            true
        );
        $kRows = $sheet->getHighestDataRow('K');
        self::assertSame(8, $kRows);
        //$lRows = $sheet->getHighestDataRow('L');
        //self::assertSame(9, $lRows);
        $lRows = 2;
        $mRows = $sheet->getHighestDataRow('M');
        self::assertSame(9, $mRows);
        $sheet->getCell('A1')
            ->setValue(
                "=FILTER(Products!M1:M$mRows,"
                    . "(Products!K1:K$kRows=D5)"
                    . "*(Products!L1:L$lRows=E5))"
            );

        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertSame([['Apples']], $result);
        $spreadsheet->disconnectWorksheets();
    }
}
