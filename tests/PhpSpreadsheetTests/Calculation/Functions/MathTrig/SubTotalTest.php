<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PHPUnit\Framework\Attributes\DataProvider;

class SubTotalTest extends AllSetupTeardown
{
    #[DataProvider('providerSUBTOTAL')]
    public function testSubtotal(float|int|string $expectedResult, float|int|string $type): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->fromArray([[0], [1], [1], [2], [3], [5], [8], [13], [21], [34], [55], [89]], null, 'A1', true);
        $maxCol = $sheet->getHighestColumn();
        $maxRow = $sheet->getHighestRow();
        $sheet->getCell('D2')->setValue("=SUBTOTAL($type, A1:$maxCol$maxRow)");
        $result = $sheet->getCell('D2')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUBTOTAL(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUBTOTAL.php';
    }

    #[DataProvider('providerSUBTOTAL')]
    public function testSubtotalColumnHidden(float|int|string $expectedResult, float|int|string $type): void
    {
        // Hidden columns don't affect calculation, only hidden rows
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->fromArray([0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89], null, 'A1', true);
        $maxCol = $sheet->getHighestColumn();
        $maxRow = $sheet->getHighestRow();
        $hiddenColumns = [
            'A' => false,
            'B' => true,
            'C' => false,
            'D' => true,
            'E' => false,
            'F' => false,
            'G' => false,
            'H' => true,
            'I' => false,
            'J' => true,
            'K' => true,
            'L' => false,
        ];
        foreach ($hiddenColumns as $col => $hidden) {
            $columnDimension = $sheet->getColumnDimension($col);
            $columnDimension->setVisible($hidden);
        }
        $sheet->getCell('D2')->setValue("=SUBTOTAL($type, A1:$maxCol$maxRow)");
        $result = $sheet->getCell('D2')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    #[DataProvider('providerSUBTOTALHIDDEN')]
    public function testSubtotalRowHidden(mixed $expectedResult, int $type): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->fromArray([[0], [1], [1], [2], [3], [5], [8], [13], [21], [34], [55], [89]], null, 'A1', true);
        $maxCol = $sheet->getHighestColumn();
        $maxRow = $sheet->getHighestRow();
        $visibleRows = [
            '1' => false,
            '2' => true,
            '3' => false,
            '4' => true,
            '5' => false,
            '6' => false,
            '7' => false,
            '8' => true,
            '9' => false,
            '10' => true,
            '11' => true,
            '12' => false,
        ];
        foreach ($visibleRows as $row => $visible) {
            $rowDimension = $sheet->getRowDimension((int) $row);
            $rowDimension->setVisible($visible);
        }
        $sheet->getCell('D2')->setValue("=SUBTOTAL($type, A1:$maxCol$maxRow)");
        $result = $sheet->getCell('D2')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUBTOTALHIDDEN(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUBTOTALHIDDEN.php';
    }

    public function testSubtotalNested(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray(
            [
                [123],
                [234],
                ['=SUBTOTAL(1,A1:A2)'],
                ['=ROMAN(SUBTOTAL(1, A1:A2))'],
                ['This is text containing "=" and "SUBTOTAL("'],
                ['=AGGREGATE(1, 0, A1:A2)'],
                ['=SUM(2, 3)'],
            ],
            null,
            'A1',
            true
        );
        $maxCol = $sheet->getHighestColumn();
        $maxRow = $sheet->getHighestRow();
        $sheet->getCell('H1')->setValue("=SUBTOTAL(9, A1:$maxCol$maxRow)");
        self::assertEquals(362, $sheet->getCell('H1')->getCalculatedValue());
    }

    public function testRefError(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=SUBTOTAL(9, #REF!)');
        self::assertEquals('#REF!', $sheet->getCell('A1')->getCalculatedValue());
    }

    public function testSecondaryRefError(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=SUBTOTAL(9, B1:B9,#REF!,C1:C9)');
        self::assertEquals('#REF!', $sheet->getCell('A1')->getCalculatedValue());
    }

    public function testNonStringSingleCellRefError(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=SUBTOTAL(9, 1, C1, Sheet99!A11)');
        self::assertEquals('#REF!', $sheet->getCell('A1')->getCalculatedValue());
    }

    public function testNonStringCellRangeRefError(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=SUBTOTAL(9, Sheet99!A1)');
        self::assertEquals('#REF!', $sheet->getCell('A1')->getCalculatedValue());
    }

    public function testTypesOfHidden(): void
    {
        $worksheet = $this->getSheet();
        $worksheet->SetCellValue('A1', 'Status');
        $worksheet->SetCellValue('B1', 'Amount');
        $worksheet->SetCellValue('A2', 'Active');
        $worksheet->SetCellValue('B2', 500);
        $worksheet->SetCellValue('A3', 'Snoozed');
        $worksheet->SetCellValue('B3', 700);
        $worksheet->SetCellValue('A4', 'Active');
        $worksheet->SetCellValue('B4', 300);

        $worksheet->setAutoFilter('A1:B4');
        $autoFilter = $worksheet->getAutoFilter();
        $autoFilter->getColumn('A')
            ->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER)
            ->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                'Active'
            );
        $autoFilter->showHideRows(); // row 3 is filtered
        $worksheet->getRowDimension(4)->setVisible(false); // row 4 is hidden but not filtered

        $worksheet->SetCellValue('D14', '=SUM(B2:B4)');
        self::assertSame(
            1500,
            $worksheet->getCell('D14')->getCalculatedValue(),
            'all 3 rows are considered'
        );
        $worksheet->SetCellValue('D15', '=SUBTOTAL(109,B2:B4)');
        self::assertSame(
            500,
            $worksheet->getCell('D15')->getCalculatedValue(),
            'excude row 3 (filtered) and row 4 (hidden)'
        );
        $worksheet->SetCellValue('D16', '=SUBTOTAL(9,B2:B4)');
        self::assertSame(
            800,
            $worksheet->getCell('D16')->getCalculatedValue(),
            'exclude row 3 (filtered) but include row 4 (hidden)'
        );
    }
}
