<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\CellReferenceHelper;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PHPUnit\Framework\TestCase;

class CellReferenceHelperTest extends TestCase
{
    /**
     * @dataProvider cellReferenceHelperInsertColumnsProvider
     */
    public function testCellReferenceHelperInsertColumns(string $expectedResult, string $cellAddress): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', 2, 0);
        $result = $cellReferenceHelper->updateCellReference($cellAddress);
        self::assertSame($expectedResult, $result);
    }

    public static function cellReferenceHelperInsertColumnsProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['D5', 'D5'],
            ['G5', 'E5'],
            'issue3363 Y5' => ['Y5', 'W5'],
            'issue3363 Z5' => ['Z5', 'X5'],
            ['AA5', 'Y5'],
            ['AB5', 'Z5'],
            ['XFC5', 'XFA5'],
            ['XFD5', 'XFB5'],
            ['XFD5', 'XFC5'],
            ['XFD5', 'XFD5'],
            ['$E5', '$E5'],
            'issue3363 $Z5' => ['$Z5', '$Z5'],
            ['$XFA5', '$XFA5'],
            ['$XFB5', '$XFB5'],
            ['$XFC5', '$XFC5'],
            ['$XFD5', '$XFD5'],
            ['G$5', 'E$5'],
            'issue3363 Y$5' => ['Y$5', 'W$5'],
            ['XFC$5', 'XFA$5'],
            ['XFD$5', 'XFB$5'],
            ['XFD$5', 'XFC$5'],
            ['XFD$5', 'XFD$5'],
            ['I5', 'G5'],
            ['$G$5', '$G$5'],
            'issue3363 $Z$5' => ['$Z$5', '$Z$5'],
            ['$XFA$5', '$XFA$5'],
            ['$XFB$5', '$XFB$5'],
            ['$XFC$5', '$XFC$5'],
            ['$XFD$5', '$XFD$5'],
        ];
    }

    /**
     * @dataProvider cellReferenceHelperDeleteColumnsProvider
     */
    public function testCellReferenceHelperDeleteColumns(string $expectedResult, string $cellAddress): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', -2, 0);
        $result = $cellReferenceHelper->updateCellReference($cellAddress);
        self::assertSame($expectedResult, $result);
    }

    public function testCantUseRange(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Only single cell references');
        $cellReferenceHelper = new CellReferenceHelper('E5', 2, 0);
        $cellReferenceHelper->updateCellReference('A1:A6');
    }

    public static function cellReferenceHelperDeleteColumnsProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['D5', 'D5'],
            ['C5', 'E5'],
            'issue3363 Y5' => ['Y5', 'AA5'],
            'issue3363 Z5' => ['Z5', 'AB5'],
            ['$E5', '$E5'],
            'issue3363 $Y5' => ['$Y5', '$Y5'],
            ['C$5', 'E$5'],
            'issue3363 Z$5' => ['Z$5', 'AB$5'],
            ['E5', 'G5'],
            ['$G$5', '$G$5'],
            'issue3363 $Z$5' => ['$Z$5', '$Z$5'],
        ];
    }

    /**
     * @dataProvider cellReferenceHelperInsertRowsProvider
     */
    public function testCellReferenceHelperInsertRows(string $expectedResult, string $cellAddress): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', 0, 2);
        $result = $cellReferenceHelper->updateCellReference($cellAddress);
        self::assertSame($expectedResult, $result);
    }

    public static function cellReferenceHelperInsertRowsProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['E4', 'E4'],
            ['E7', 'E5'],
            ['E1048575', 'E1048573'],
            ['E1048576', 'E1048574'],
            ['E1048576', 'E1048575'],
            ['E1048576', 'E1048576'],
            'issue3363 Y5' => ['Y7', 'Y5'],
            'issue3363 Z5' => ['Z7', 'Z5'],
            ['E$5', 'E$5'],
            'issue3363 Y$5' => ['Y$5', 'Y$5'],
            ['$E7', '$E5'],
            'issue3363 $Z5' => ['$Z7', '$Z5'],
            ['E11', 'E9'],
            ['$E$9', '$E$9'],
            'issue3363 $Z$5' => ['$Z$5', '$Z$5'],
        ];
    }

    /**
     * @dataProvider cellReferenceHelperDeleteRowsProvider
     */
    public function testCellReferenceHelperDeleteRows(string $expectedResult, string $cellAddress): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', 0, -2);
        $result = $cellReferenceHelper->updateCellReference($cellAddress);
        self::assertSame($expectedResult, $result);
    }

    public static function cellReferenceHelperDeleteRowsProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['E4', 'E4'],
            ['E3', 'E5'],
            'issue3363 Y5' => ['Y3', 'Y5'],
            'issue3363 Z5' => ['Z3', 'Z5'],
            ['E$5', 'E$5'],
            'issue3363 Y$5' => ['Y$5', 'Y$5'],
            ['$E3', '$E5'],
            'issue3363 $Z5' => ['$Z3', '$Z5'],
            ['E7', 'E9'],
            ['$E$9', '$E$9'],
            'issue3363 $Z$5' => ['$Z$5', '$Z$5'],
        ];
    }

    /**
     * @dataProvider cellReferenceHelperInsertColumnsAbsoluteProvider
     */
    public function testCellReferenceHelperInsertColumnsAbsolute(string $expectedResult, string $cellAddress): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', 2, 0);
        $result = $cellReferenceHelper->updateCellReference($cellAddress, true);
        self::assertSame($expectedResult, $result);
    }

    public static function cellReferenceHelperInsertColumnsAbsoluteProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['D5', 'D5'],
            ['G5', 'E5'],
            'issue3363 Y5' => ['Y5', 'W5'],
            'issue3363 Z5' => ['Z5', 'X5'],
            ['$G5', '$E5'],
            'issue3363 $Y5' => ['$Y5', '$W5'],
            ['G$5', 'E$5'],
            'issue3363 Y$5' => ['Y$5', 'W$5'],
            ['I5', 'G5'],
            ['$I$5', '$G$5'],
            'issue3363 $Y$5' => ['$Y$5', '$W$5'],
        ];
    }

    /**
     * @dataProvider cellReferenceHelperDeleteColumnsAbsoluteProvider
     */
    public function testCellReferenceHelperDeleteColumnsAbsolute(string $expectedResult, string $cellAddress): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', -2, 0);
        $result = $cellReferenceHelper->updateCellReference($cellAddress, true);
        self::assertSame($expectedResult, $result);
    }

    public static function cellReferenceHelperDeleteColumnsAbsoluteProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['D5', 'D5'],
            ['C5', 'E5'],
            'issue3363 Y5' => ['Y5', 'AA5'],
            'issue3363 Z5' => ['Z5', 'AB5'],
            ['$C5', '$E5'],
            'issue3363 $Y5' => ['$Y5', '$AA5'],
            ['C$5', 'E$5'],
            'issue3363 Z$5' => ['Z$5', 'AB$5'],
            ['E5', 'G5'],
            ['$E$5', '$G$5'],
            'issue3363 $Z$5' => ['$Z$5', '$AB$5'],
        ];
    }

    /**
     * @dataProvider cellReferenceHelperInsertRowsAbsoluteProvider
     */
    public function testCellReferenceHelperInsertRowsAbsolute(string $expectedResult, string $cellAddress): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', 0, 2);
        $result = $cellReferenceHelper->updateCellReference($cellAddress, true);
        self::assertSame($expectedResult, $result);
    }

    public static function cellReferenceHelperInsertRowsAbsoluteProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['E4', 'E4'],
            ['E7', 'E5'],
            'issue3363 Y5' => ['Y7', 'Y5'],
            'issue3363 Z5' => ['Z7', 'Z5'],
            ['E$7', 'E$5'],
            'issue3363 Y$5' => ['Y$7', 'Y$5'],
            ['$E7', '$E5'],
            'issue3363 $Y5' => ['$Y7', '$Y5'],
            ['E11', 'E9'],
            ['$E$11', '$E$9'],
            'issue3363 $Z$5' => ['$Z$7', '$Z$5'],
        ];
    }

    /**
     * @dataProvider cellReferenceHelperDeleteRowsAbsoluteProvider
     */
    public function testCellReferenceHelperDeleteRowsAbsolute(string $expectedResult, string $cellAddress): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', 0, -2);
        $result = $cellReferenceHelper->updateCellReference($cellAddress, true);
        self::assertSame($expectedResult, $result);
    }

    public static function cellReferenceHelperDeleteRowsAbsoluteProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['E4', 'E4'],
            ['E3', 'E5'],
            'issue3363 Y5' => ['Y3', 'Y5'],
            'issue3363 Z5' => ['Z3', 'Z5'],
            ['E$3', 'E$5'],
            'issue3363 Y$5' => ['Y$3', 'Y$5'],
            ['$E3', '$E5'],
            'issue3363 $Z5' => ['$Z3', '$Z5'],
            ['E7', 'E9'],
            ['$E$7', '$E$9'],
            'issue3363 $Z$5' => ['$Z$3', '$Z$5'],
        ];
    }

    public function testCellReferenceHelperDeleteColumnAltogether(): void
    {
        $cellReferenceHelper = new CellReferenceHelper('E5', -4, 0);
        self::assertTrue($cellReferenceHelper->cellAddressInDeleteRange('A5'));
    }
}
