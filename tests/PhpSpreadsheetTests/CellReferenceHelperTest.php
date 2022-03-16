<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\CellReferenceHelper;
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

    public function cellReferenceHelperInsertColumnsProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['D5', 'D5'],
            ['G5', 'E5'],
            ['$E5', '$E5'],
            ['G$5', 'E$5'],
            ['I5', 'G5'],
            ['$G$5', '$G$5'],
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

    public function cellReferenceHelperDeleteColumnsProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['D5', 'D5'],
            ['C5', 'E5'],
            ['$E5', '$E5'],
            ['C$5', 'E$5'],
            ['E5', 'G5'],
            ['$G$5', '$G$5'],
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

    public function cellReferenceHelperInsertRowsProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['E4', 'E4'],
            ['E7', 'E5'],
            ['E$5', 'E$5'],
            ['$E7', '$E5'],
            ['E11', 'E9'],
            ['$E$9', '$E$9'],
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

    public function cellReferenceHelperDeleteRowsProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['E4', 'E4'],
            ['E3', 'E5'],
            ['E$5', 'E$5'],
            ['$E3', '$E5'],
            ['E7', 'E9'],
            ['$E$9', '$E$9'],
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

    public function cellReferenceHelperInsertColumnsAbsoluteProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['D5', 'D5'],
            ['G5', 'E5'],
            ['$G5', '$E5'],
            ['G$5', 'E$5'],
            ['I5', 'G5'],
            ['$I$5', '$G$5'],
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

    public function cellReferenceHelperDeleteColumnsAbsoluteProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['D5', 'D5'],
            ['C5', 'E5'],
            ['$C5', '$E5'],
            ['C$5', 'E$5'],
            ['E5', 'G5'],
            ['$E$5', '$G$5'],
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

    public function cellReferenceHelperInsertRowsAbsoluteProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['E4', 'E4'],
            ['E7', 'E5'],
            ['E$7', 'E$5'],
            ['$E7', '$E5'],
            ['E11', 'E9'],
            ['$E$11', '$E$9'],
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

    public function cellReferenceHelperDeleteRowsAbsoluteProvider(): array
    {
        return [
            ['A1', 'A1'],
            ['E4', 'E4'],
            ['E3', 'E5'],
            ['E$3', 'E$5'],
            ['$E3', '$E5'],
            ['E7', 'E9'],
            ['$E$7', '$E$9'],
        ];
    }
}
