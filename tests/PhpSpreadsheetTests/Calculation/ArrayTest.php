<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase
{
    public function testMultiDimensionalArrayIsFlattened(): void
    {
        $array = [
            0 => [
                0 => [
                    32 => [
                        'B' => 'PHP',
                    ],
                ],
            ],
            1 => [
                0 => [
                    32 => [
                        'C' => 'Spreadsheet',
                    ],
                ],
            ],
        ];

        $values = Functions::flattenArray($array);

        self::assertIsNotArray($values[0]);
        self::assertIsNotArray($values[1]);
    }

    public function testPropagateStatic(): void
    {
        $oldValue = Calculation::getArrayReturnType();
        $calculation = new Calculation();
        self::assertTrue(Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY));
        self::assertFalse(Calculation::setArrayReturnType('xxx'));
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, Calculation::getArrayReturnType());
        self::assertFalse($calculation->setArrayReturnType('xxx'));
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, $calculation->getInstanceArrayReturnType());
        self::assertTrue($calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ERROR));
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, Calculation::getArrayReturnType());
        self::assertSame(Calculation::RETURN_ARRAY_AS_ERROR, $calculation->getInstanceArrayReturnType());
        Calculation::setArrayReturnType($oldValue);
    }

    public function testReturnTypes(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $sheet->setCellValue('A1', 2.0);
        $sheet->setCellValue('A2', 0.0);
        $sheet->setCellValue('B1', 0.0);
        $sheet->setCellValue('B2', 1.0);
        $sheet->setCellValue('D1', '=MINVERSE(A1:B2)');
        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ERROR);
        self::assertSame('#VALUE!', $sheet->getCell('D1')->getCalculatedValue());
        $calculation->flushInstance();
        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);
        self::assertSame(0.5, $sheet->getCell('D1')->getCalculatedValue());
        $calculation->flushInstance();
        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        self::assertSame([[0.5, 0.0], [0.0, 1.0]], $sheet->getCell('D1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
