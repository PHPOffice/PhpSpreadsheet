<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PHPUnit\Framework\Attributes\DataProvider;

class OffsetTest extends AllSetupTeardown
{
    #[DataProvider('providerOFFSET')]
    public function testOFFSET(mixed $expectedResult, null|string $cellReference = null): void
    {
        $result = LookupRef\Offset::OFFSET($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public static function providerOFFSET(): array
    {
        return require 'tests/data/Calculation/LookupRef/OFFSET.php';
    }

    public function testOffsetSpreadsheet(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('B6')->setValue(4);
        $sheet->getCell('B7')->setValue(8);
        $sheet->getCell('B8')->setValue(3);
        $sheet->getCell('D6')->setValue(10);
        $sheet->getCell('D7')->setValue(3);
        $sheet->getCell('D8')->setValue(6);

        $sheet->getCell('A1')->setValue('=OFFSET(D3,3,-2,1,1)');
        self::assertSame(4, $sheet->getCell('A1')->getCalculatedValue());
        $sheet->getCell('A2')->setValue('=SUM(OFFSET(D3:F5,3,-2, 3, 3))');
        self::assertSame(34, $sheet->getCell('A2')->getCalculatedValue());
        $sheet->getCell('A3')->setValue('=OFFSET(D3, -3, -3)');
        self::assertSame('#REF!', $sheet->getCell('A3')->getCalculatedValue());

        $sheet->getCell('C1')->setValue(5);
        $sheet->getCell('A4')->setValue('=OFFSET(C1, 0, 0, 0, 0)');
        self::assertSame('#REF!', $sheet->getCell('A4')->getCalculatedValue());
        $sheet->getCell('A5')->setValue('=OFFSET(C1, 0, 0)');
        self::assertSame(5, $sheet->getCell('A5')->getCalculatedValue());
    }

    public function testOffsetNamedRange(): void
    {
        $workSheet = $this->getSheet();
        $workSheet->setCellValue('A1', 1);
        $workSheet->setCellValue('A2', 2);

        $this->getSpreadsheet()->addNamedRange(new NamedRange('demo', $workSheet, '=$A$1'));

        $workSheet->setCellValue('B1', '=demo');
        $workSheet->setCellValue('B2', '=OFFSET(demo, 1, 0)');

        self::assertSame(2, $workSheet->getCell('B2')->getCalculatedValue());
    }

    public function testOffsetNamedRangeApostropheSheet(): void
    {
        $workSheet = $this->getSheet();
        $workSheet->setTitle("apo'strophe");
        $workSheet->setCellValue('A1', 1);
        $workSheet->setCellValue('A2', 2);

        $this->getSpreadsheet()->addNamedRange(new NamedRange('demo', $workSheet, '=$A$1'));

        $workSheet->setCellValue('B1', '=demo');
        $workSheet->setCellValue('B2', '=OFFSET(demo, 1, 0)');

        self::assertSame(2, $workSheet->getCell('B2')->getCalculatedValue());
    }

    public function testOffsetMultiCellNamedRange(): void
    {
        $sheet = $this->getSheet();
        $sheet->setCellValue('D13', 'Hello');
        $this->getSpreadsheet()
            ->addNamedRange(new NamedRange('CELLAREA', $sheet, '$B$6:$F$22'));
        $sheet->setCellValue('D1', '=OFFSET(CELLAREA,7,2,1,1)');
        self::assertSame('Hello', $sheet->getCell('D1')->getCalculatedValue());
    }

    /** @return array<string, array{mixed, string}> */
    public static function providerFunctionReference(): array
    {
        return [
            'INDEX single cell' => [37, '=OFFSET(INDEX(Data!A1:A5,2),1,0)'],
            'INDEX with column' => [41, '=OFFSET(INDEX(Data!A1:B5,4,2),0,-1)'],
            'INDEX across a row' => [9, '=OFFSET(INDEX(Data!A7:C7,2),0,1)'],
            'INDEX whole column' => [11 + 23 + 37, '=SUM(OFFSET(INDEX(Data!A1:A5,2),-1,0,3,1))'],
            'CHOOSE' => [37, '=OFFSET(CHOOSE(1,Data!A2),1,0)'],
            'CHOOSE second' => [41, '=OFFSET(CHOOSE(2,Data!A1,Data!A3),1,0)'],
            'IF true' => [37, '=OFFSET(IF(TRUE,Data!A2,Data!A1),1,0)'],
            'IF false' => [23, '=OFFSET(IF(FALSE,Data!A2,Data!A1),1,0)'],
            'direct reference' => [37, '=OFFSET(Data!A2,1,0)'],
            'range with height' => [78, '=SUM(OFFSET(INDEX(Data!A1:A5,2),1,0,2,1))'],
        ];
    }

    #[DataProvider('providerFunctionReference')]
    public function testOffsetOfFunctionReturningReferenceOnOtherSheet(int $expected, string $formula): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $calc = $this->getSheet();
        $calc->setTitle('Calc');
        $data = $spreadsheet->createSheet();
        $data->setTitle('Data');
        $data->fromArray([[11, 1], [23, 2], [37, 3], [41, 4], [59, 5]], null, 'A1');
        $data->fromArray([[7, 8, 9]], null, 'A7');
        $calc->getCell('A1')->setValue($formula);
        self::assertSame($expected, $calc->getCell('A1')->getCalculatedValue());
    }
}
