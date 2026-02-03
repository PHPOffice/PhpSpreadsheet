<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class ConcatenateRangeTest extends AllSetupTeardown
{
    public function testIssue4061(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('a');
        $sheet->getCell('A2')->setValue('b');
        $sheet->getCell('A3')->setValue('c');
        $sheet->getCell('C1')->setValue('1');
        $sheet->getCell('C2')->setValue('2');
        $sheet->getCell('C3')->setValue('3');
        $sheet->getCell('B1')->setValue('=CONCATENATE(A1:A3, "-", C1:C3)');
        Calculation::getInstance($this->getSpreadsheet())
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_VALUE
            );
        self::assertSame('a-1', $sheet->getCell('B1')->getCalculatedValue());
        $sheet->getCell('X1')->setValue('=A1:A3&"-"&C1:C3');
        self::assertSame('a-1', $sheet->getCell('X1')->getCalculatedValue());
        $sheet->getCell('D1')->setValue('=CONCAT(A1:A3, "-", C1:C3)');
        self::assertSame('abc-123', $sheet->getCell('D1')->getCalculatedValue());
        Calculation::getInstance($this->getSpreadsheet())
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet->getCell('E1')->setValue('=CONCATENATE(A1:A3, "-", C1:C3)');
        self::assertSame([['a-1'], ['b-2'], ['c-3']], $sheet->getCell('E1')->getCalculatedValue());
        $sheet->getCell('Y1')->setValue('=A1:A3&"-"&C1:C3');
        self::assertSame([['a-1'], ['b-2'], ['c-3']], $sheet->getCell('Y1')->getCalculatedValue());
        $sheet->getCell('F1')->setValue('=CONCAT(A1:A3, "-", C1:C3)');
        self::assertSame('abc-123', $sheet->getCell('F1')->getCalculatedValue());
    }

    public function testIssue4061Value(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('a');
        $sheet->getCell('A2')->setValue('b');
        $sheet->getCell('A3')->setValue('c');
        $sheet->getCell('C1')->setValue('1');
        $sheet->getCell('C2')->setValue('2');
        $sheet->getCell('C3')->setValue('3');
        $sheet->getCell('B1')->setValue('=CONCATENATE(A:A, "-", C:C)');
        $sheet->getCell('B2')->setValue('=CONCATENATE(A:A, "-", C:C)');
        $sheet->getCell('B3')->setValue('=CONCATENATE(A:A, "-", C:C)');
        Calculation::getInstance($this->getSpreadsheet())
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_VALUE
            );
        self::assertSame('a-1', $sheet->getCell('B1')->getCalculatedValue());
        self::assertSame('b-2', $sheet->getCell('B2')->getCalculatedValue());
        self::assertSame('c-3', $sheet->getCell('B3')->getCalculatedValue());
        $sheet->getCell('F1')
            ->setValue('=CONCATENATE("X", C2:C3, "Y")');
        self::assertSame('#VALUE!', $sheet->getCell('F1')->getCalculatedValue(), 'row does not match range');
    }

    public function testConvertCellRangeEdgeCases(): void
    {
        $array1 = [
            1 => ['A' => 'a', 'B' => 'd'],
            'B' => ['A' => 'b', 'B' => 'e'],
            3 => ['A' => 'c', 'B' => 'f'],
        ];
        self::assertSame('', Functions::convertArrayToCellRange($array1));
        $array2 = [
            1 => ['A' => 'a', 'B' => 'd'],
            2 => ['A' => 'b', 6 => 'e'],
            3 => ['A' => 'c', 'B' => 'f'],
        ];
        self::assertSame('', Functions::convertArrayToCellRange($array2));
        $array3 = [
            1 => ['A' => 'a', 'B' => 'd'],
            2 => ['A' => 'b', 'B' => 'e'],
            3 => ['A' => 'c', 'B' => 'f'],
        ];
        self::assertSame('A1:B3', Functions::convertArrayToCellRange($array3));
    }
}
