<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class SubTotalTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSUBTOTAL
     *
     * @param mixed $expectedResult
     * @param mixed $type expect an integer
     */
    public function testSubtotal($expectedResult, $type): void
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

    public function providerSUBTOTAL(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUBTOTAL.php';
    }

    /**
     * @dataProvider providerSUBTOTAL
     *
     * @param mixed $expectedResult
     * @param mixed $type expect an integer
     */
    public function testSubtotalColumnHidden($expectedResult, $type): void
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

    /**
     * @dataProvider providerSUBTOTALHIDDEN
     *
     * @param mixed $expectedResult
     * @param mixed $type expect an integer
     */
    public function testSubtotalRowHidden($expectedResult, $type): void
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
            $rowDimension = $sheet->getRowDimension($row);
            $rowDimension->setVisible($visible);
        }
        $sheet->getCell('D2')->setValue("=SUBTOTAL($type, A1:$maxCol$maxRow)");
        $result = $sheet->getCell('D2')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSUBTOTALHIDDEN(): array
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
}
