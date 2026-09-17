<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Attributes\DataProvider;

class VLookupTest extends AllSetupTeardown
{
    #[DataProvider('providerVLOOKUP')]
    public function testVLOOKUP(mixed $expectedResult, mixed $value, mixed $table, mixed $index, ?bool $lookup = null): void
    {
        $this->setArrayAsValue();
        $sheet = $this->getSheet();
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
    }

    public static function providerVLOOKUP(): array
    {
        return require 'tests/data/Calculation/LookupRef/VLOOKUP.php';
    }

    #[DataProvider('providerVLookupArray')]
    public function testVLookupArray(array $expectedResult, string $values, string $database, string $index): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=VLOOKUP({$values}, {$database}, {$index}, false)";
        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
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
        $worksheet = $this->getSheet();

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
    }

    /**
     * VLOOKUP with a whole-column range where the end column contains no data.
     * getHighestDataRow() on the empty end column returned 1, producing an
     * inverted range (e.g. A4:F1) that caused a #N/A result.
     *
     * @see https://github.com/PHPOffice/PhpSpreadsheet/pull/4967
     */
    #[DataProvider('providerVlookupWholeColumnRange')]
    public function testVlookupWholeColumnRange(string $formula, string $expectedResult): void
    {
        $spreadsheet = $this->getSpreadsheet();

        // Lookup sheet: col A = keys, col C = values; cols B, D, E, F are empty
        $lookupSheet = new Worksheet($spreadsheet, 'Sheet2');
        $spreadsheet->addSheet($lookupSheet, 0);
        $lookupSheet->fromArray([
            [1234, null, 'row1'],
            [2345, null, 'row2'],
            [3456, null, 'row3'],
            [4567, null, 'row4'],
        ], null, 'A1');

        // Formula sheet: C1 holds the lookup value
        $formulaSheet = new Worksheet($spreadsheet, 'Sheet1');
        $spreadsheet->addSheet($formulaSheet, 1);
        $formulaSheet->setCellValue('C1', 3456);
        $formulaSheet->setCellValue('A1', $formula);
        $spreadsheet->setActiveSheetIndexByName('Sheet1');

        $result = $formulaSheet->getCell('A1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerVlookupWholeColumnRange(): array
    {
        return [
            'VLOOKUP with absolute whole-column range across sheets' => [
                '=VLOOKUP($C1,Sheet2!$A:$F,3,FALSE)',
                'row3',
            ],
            'VLOOKUP with relative whole-column range across sheets' => [
                '=VLOOKUP($C1,Sheet2!A:F,3,FALSE)',
                'row3',
            ],
            'VLOOKUP with mixed whole-column range across sheets' => [
                '=VLOOKUP($C1,Sheet2!A:$F,3,FALSE)',
                'row3',
            ],
        ];
    }
}
