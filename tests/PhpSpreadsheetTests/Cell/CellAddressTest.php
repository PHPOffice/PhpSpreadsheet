<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CellAddressTest extends TestCase
{
    /**
     * @dataProvider providerCreateFromCellAddress
     */
    public function testCreateFromCellAddress(
        string $cellAddress,
        string $expectedColumnName,
        int $expectedColumnId,
        int $expectedRowId
    ): void {
        $cellAddressObject = CellAddress::fromCellAddress($cellAddress);

        self::assertSame($cellAddress, (string) $cellAddressObject);
        self::assertSame($cellAddress, $cellAddressObject->cellAddress());
        self::assertSame($expectedRowId, $cellAddressObject->rowId());
        self::assertSame($expectedColumnId, $cellAddressObject->columnId());
        self::assertSame($expectedColumnName, $cellAddressObject->columnName());
    }

    public static function providerCreateFromCellAddress(): array
    {
        return [
            ['A1', 'A', 1, 1],
            ['C5', 'C', 3, 5],
            ['IV256', 'IV', 256, 256],
        ];
    }

    /**
     * @dataProvider providerCreateFromCellAddressException
     */
    public function testCreateFromCellAddressException(string $cellAddress): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            $cellAddress === ''
                ? 'Cell coordinate can not be zero-length string'
                : "Invalid cell coordinate {$cellAddress}"
        );

        CellAddress::fromCellAddress($cellAddress);
    }

    public static function providerCreateFromCellAddressException(): array
    {
        return [
            ['INVALID'],
            [''],
            ['IV'],
            ['12'],
        ];
    }

    /**
     * @dataProvider providerCreateFromColumnAndRow
     */
    public function testCreateFromColumnAndRow(
        int $columnId,
        int $rowId,
        string $expectedCellAddress,
        string $expectedColumnName
    ): void {
        $cellAddressObject = CellAddress::fromColumnAndRow($columnId, $rowId);

        self::assertSame($expectedCellAddress, (string) $cellAddressObject);
        self::assertSame($expectedCellAddress, $cellAddressObject->cellAddress());
        self::assertSame($rowId, $cellAddressObject->rowId());
        self::assertSame($columnId, $cellAddressObject->columnId());
        self::assertSame($expectedColumnName, $cellAddressObject->columnName());
    }

    /**
     * @dataProvider providerCreateFromColumnRowException
     */
    public function testCreateFromColumnRowException(int|string $columnId, int|string $rowId): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Row and Column Ids must be positive integer values');

        CellAddress::fromColumnAndRow($columnId, $rowId);
    }

    public static function providerCreateFromColumnAndRow(): array
    {
        return [
            [1, 1, 'A1', 'A'],
            [3, 5, 'C5', 'C'],
            [256, 256, 'IV256', 'IV'],
        ];
    }

    /**
     * @dataProvider providerCreateFromColumnRowArray
     */
    public function testCreateFromColumnRowArray(
        int $columnId,
        int $rowId,
        string $expectedCellAddress,
        string $expectedColumnName
    ): void {
        $columnRowArray = [$columnId, $rowId];
        $cellAddressObject = CellAddress::fromColumnRowArray($columnRowArray);

        self::assertSame($expectedCellAddress, (string) $cellAddressObject);
        self::assertSame($expectedCellAddress, $cellAddressObject->cellAddress());
        self::assertSame($rowId, $cellAddressObject->rowId());
        self::assertSame($columnId, $cellAddressObject->columnId());
        self::assertSame($expectedColumnName, $cellAddressObject->columnName());
    }

    public static function providerCreateFromColumnRowArray(): array
    {
        return [
            [1, 1, 'A1', 'A'],
            [3, 5, 'C5', 'C'],
            [256, 256, 'IV256', 'IV'],
        ];
    }

    /**
     * @dataProvider providerCreateFromColumnRowException
     */
    public function testCreateFromColumnRowArrayException(mixed $columnId, mixed $rowId): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Row and Column Ids must be positive integer values');

        $columnRowArray = [$columnId, $rowId];
        CellAddress::fromColumnRowArray($columnRowArray);
    }

    public static function providerCreateFromColumnRowException(): array
    {
        return [
            [-1, 1],
            [3, 'A'],
        ];
    }

    /**
     * @dataProvider providerCreateFromCellAddressWithWorksheet
     */
    public function testCreateFromCellAddressWithWorksheet(
        string $cellAddress,
        string $expectedCellAddress,
        string $expectedColumnName,
        int $expectedColumnId,
        int $expectedRowId
    ): void {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle("Mark's Worksheet");

        $cellAddressObject = CellAddress::fromCellAddress($cellAddress, $worksheet);

        self::assertSame($expectedCellAddress, (string) $cellAddressObject);
        self::assertSame($cellAddress, $cellAddressObject->cellAddress());
        self::assertSame($expectedRowId, $cellAddressObject->rowId());
        self::assertSame($expectedColumnId, $cellAddressObject->columnId());
        self::assertSame($expectedColumnName, $cellAddressObject->columnName());
    }

    public static function providerCreateFromCellAddressWithWorksheet(): array
    {
        return [
            ['A1', "'Mark''s Worksheet'!A1", 'A', 1, 1],
            ['C5', "'Mark''s Worksheet'!C5", 'C', 3, 5],
            ['IV256', "'Mark''s Worksheet'!IV256", 'IV', 256, 256],
        ];
    }

    public function testNextRow(): void
    {
        $cellAddress = CellAddress::fromCellAddress('C5');
        // default single row
        $cellAddressC6 = $cellAddress->nextRow();
        self::assertSame('C6', (string) $cellAddressC6);
        // multiple rows
        $cellAddressC9 = $cellAddress->nextRow(4);
        self::assertSame('C9', (string) $cellAddressC9);
        // negative rows
        $cellAddressC3 = $cellAddress->nextRow(-2);
        self::assertSame('C3', (string) $cellAddressC3);
        // negative beyond the minimum
        $cellAddressC1 = $cellAddress->nextRow(-10);
        self::assertSame('C1', (string) $cellAddressC1);

        // Check that the original object is still unchanged
        self::assertSame('C5', (string) $cellAddress);
    }

    public function testPreviousRow(): void
    {
        $cellAddress = CellAddress::fromCellAddress('C5');
        // default single row
        $cellAddressC4 = $cellAddress->previousRow();
        self::assertSame('C4', (string) $cellAddressC4);
    }

    public function testNextColumn(): void
    {
        $cellAddress = CellAddress::fromCellAddress('C5');
        // default single row
        $cellAddressD5 = $cellAddress->nextColumn();
        self::assertSame('D5', (string) $cellAddressD5);
        // multiple rows
        $cellAddressG5 = $cellAddress->nextColumn(4);
        self::assertSame('G5', (string) $cellAddressG5);
        // negative rows
        $cellAddressB5 = $cellAddress->nextColumn(-1);
        self::assertSame('B5', (string) $cellAddressB5);
        // negative beyond the minimum
        $cellAddressA5 = $cellAddress->nextColumn(-10);
        self::assertSame('A5', (string) $cellAddressA5);

        // Check that the original object is still unchanged
        self::assertSame('C5', (string) $cellAddress);
    }

    public function testPreviousColumn(): void
    {
        $cellAddress = CellAddress::fromCellAddress('C5');
        // default single row
        $cellAddressC4 = $cellAddress->previousColumn();
        self::assertSame('B5', (string) $cellAddressC4);
    }
}
