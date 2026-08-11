<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase
{
    private string $originalArrayReturnType;

    protected function setUp(): void
    {
        $this->originalArrayReturnType = Calculation::getArrayReturnType();
    }

    protected function tearDown(): void
    {
        Calculation::setArrayReturnType($this->originalArrayReturnType);
    }

    private function setupMatrix(Worksheet $sheet): void
    {
        $sheet->setCellValue('A1', 2.0);
        $sheet->setCellValue('A2', 0.0);
        $sheet->setCellValue('B1', 0.0);
        $sheet->setCellValue('B2', 1.0);
    }

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

        self::assertCount(2, $values);
        self::assertSame('PHP', $values[0]);
        self::assertSame('Spreadsheet', $values[1]);
    }

    public function testSetArrayReturnTypeWithValidValue(): void
    {
        self::assertTrue(Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY));
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, Calculation::getArrayReturnType());
    }

    public function testSetArrayReturnTypeWithInvalidValue(): void
    {
        $originalType = Calculation::getArrayReturnType();
        self::assertFalse(Calculation::setArrayReturnType('xxx'));
        self::assertSame($originalType, Calculation::getArrayReturnType());
    }

    public function testInstanceArrayReturnTypeInheritsFromStatic(): void
    {
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $calculation = new Calculation();
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, $calculation->getInstanceArrayReturnType());
    }

    public function testInstanceArrayReturnTypeCanBeOverridden(): void
    {
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $calculation = new Calculation();
        self::assertTrue($calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ERROR));
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, Calculation::getArrayReturnType());
        self::assertSame(Calculation::RETURN_ARRAY_AS_ERROR, $calculation->getInstanceArrayReturnType());
    }

    public function testSetInstanceArrayReturnTypeWithInvalidValue(): void
    {
        $calculation = new Calculation();
        self::assertFalse($calculation->setInstanceArrayReturnType('xxx'));
    }

    public function testReturnTypeAsError(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $this->setupMatrix($sheet);
        $sheet->setCellValue('D1', '=MINVERSE(A1:B2)');

        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ERROR);
        self::assertSame('#VALUE!', $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testReturnTypeAsValue(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $this->setupMatrix($sheet);
        $sheet->setCellValue('D1', '=MINVERSE(A1:B2)');

        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);
        self::assertSame(0.5, $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testReturnTypeAsArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $this->setupMatrix($sheet);
        $sheet->setCellValue('D1', '=MINVERSE(A1:B2)');

        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        self::assertSame([[0.5, 0.0], [0.0, 1.0]], $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }
}
