<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class VLookupTest extends TestCase
{
    /**
     * @dataProvider providerVLOOKUP
     */
    public function testVLOOKUP(mixed $expectedResult, mixed $value, mixed $table, mixed $index, ?bool $lookup = null): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if (is_array($table)) {
            $sheet->fromArray($table);
            $dimension = $sheet->calculateWorksheetDimension();
        } else {
            $sheet->getCell('A1')->setValue($table);
            $dimension = 'A1';
        }
        if ($lookup === null) {
            $lastarg = '';
        } else {
            $lastarg = $lookup ? ',TRUE' : ',FALSE';
        }
        $sheet->getCell('Z98')->setValue($value);
        if (is_array($index)) {
            $sheet->fromArray($index, null, 'Z100', true);
            $indexarg = 'Z100:Z' . (string) (99 + count($index));
        } else {
            $sheet->getCell('Z100')->setValue($index);
            $indexarg = 'Z100';
        }

        $sheet->getCell('Z99')->setValue("=VLOOKUP(Z98,$dimension,$indexarg$lastarg)");
        $result = $sheet->getCell('Z99')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerVLOOKUP(): array
    {
        return require 'tests/data/Calculation/LookupRef/VLOOKUP.php';
    }

    /**
     * @dataProvider providerVLookupArray
     */
    public function testVLookupArray(array $expectedResult, string $values, string $database, string $index): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=VLOOKUP({$values}, {$database}, {$index}, false)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerVLookupArray(): array
    {
        return [
            'row vector' => [
                [[4.19, 5.77, 4.14]],
                '{"Orange", "Green", "Red"}',
                '{"Red", 4.14; "Orange", 4.19; "Yellow", 5.17; "Green", 5.77; "Blue", 6.39}',
                '2',
            ],
            'issue 3561' => [
                [[7, 8, 7]],
                '6',
                '{1,2,3,4,5;6,7,8,9,10;11,12,13,14,15}',
                '{2,3,2}',
            ],
        ];
    }

    public function testIssue1402(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->setCellValueExplicit('A1', 1, DataType::TYPE_STRING);
        $worksheet->setCellValue('B1', 'Text Nr 1');
        $worksheet->setCellValue('A2', 2);
        $worksheet->setCellValue('B2', 'Numeric result');
        $worksheet->setCellValueExplicit('A3', 2, DataType::TYPE_STRING);
        $worksheet->setCellValue('B3', 'Text Nr 2');
        $worksheet->setCellValueExplicit('A4', 2, DataType::TYPE_STRING);
        $worksheet->setCellValue('B4', '=VLOOKUP(A4,$A$1:$B$3,2,0)');
        self::assertSame('Text Nr 2', $worksheet->getCell('B4')->getCalculatedValue());
        $worksheet->setCellValue('A5', 2);
        $worksheet->setCellValue('B5', '=VLOOKUP(A5,$A$1:$B$3,2,0)');
        self::assertSame('Numeric result', $worksheet->getCell('B5')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
