<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Collection;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Collection\Memory;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class CellsTest extends TestCase
{
    public function testCollectionCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $collection = $sheet->getCellCollection();

        // Assert empty state
        self::assertEquals([], $collection->getCoordinates(), 'cell list should be empty');
        self::assertEquals([], $collection->getSortedCoordinates(), 'sorted cell list should be empty');
        $getB2 = $collection->get('B2');
        self::assertNull($getB2, 'getting non-existing cell must return null');
        self::assertFalse($collection->has('B2'), 'non-existing cell should be non-existent');

        // Add one cell
        $cell1 = $sheet->getCell('B2');
        self::assertSame($cell1, $collection->add('B2', $cell1), 'adding a cell should return the cell');

        // Assert cell presence
        self::assertEquals(['B2'], $collection->getCoordinates(), 'cell list should contains the B2 cell');
        self::assertEquals(['B2'], $collection->getSortedCoordinates(), 'sorted cell list contains the B2 cell');
        self::assertSame($cell1, $collection->get('B2'), 'should get exact same object');
        self::assertTrue($collection->has('B2'), 'B2 cell should exists');

        // Add a second cell
        $cell2 = $sheet->getCell('A1');
        self::assertSame($cell2, $collection->add('A1', $cell2), 'adding a second cell should return the cell');
        self::assertEquals(['B2', 'A1'], $collection->getCoordinates(), 'cell list should contains the second cell');
        // Note that sorts orders the collection itself, so these cells will aread be ordered for the subsequent asserions
        self::assertEquals(['A1', 'B2'], $collection->getSortedCoordinates(), 'sorted cell list contains the second cell');

        // Add a third cell
        $cell3 = $sheet->getCell('AA1');
        self::assertSame($cell3, $collection->add('AA1', $cell3), 'adding a third cell should return the cell');
        self::assertEquals(['A1', 'B2', 'AA1'], $collection->getCoordinates(), 'cell list should contains the third cell');
        // Note that sorts orders the collection itself, so these cells will aread be ordered for the subsequent asserions
        self::assertEquals(['A1', 'AA1', 'B2'], $collection->getSortedCoordinates(), 'sorted cell list contains the third cell');

        // Add a fourth cell
        $cell4 = $sheet->getCell('Z1');
        self::assertSame($cell4, $collection->add('Z1', $cell4), 'adding a fourth cell should return the cell');
        self::assertEquals(['A1', 'AA1', 'B2', 'Z1'], $collection->getCoordinates(), 'cell list should contains the fourth cell');
        // Note that sorts orders the collection itself, so these cells will aread be ordered for the subsequent asserions
        self::assertEquals(['A1', 'Z1', 'AA1', 'B2'], $collection->getSortedCoordinates(), 'sorted cell list contains the fourth cell');

        // Assert collection copy
        $sheet2 = $spreadsheet->createSheet();
        $collection2 = $collection->cloneCellCollection($sheet2);
        self::assertTrue($collection2->has('A1'));
        $copiedCell2 = $collection2->get('A1');
        self::assertNotNull($copiedCell2);
        self::assertNotSame($cell2, $copiedCell2, 'copied cell should not be the same object any more');
        self::assertSame($collection2, $copiedCell2->getParent(), 'copied cell should be owned by the copied collection');
        self::assertSame('A1', $copiedCell2->getCoordinate(), 'copied cell should keep attributes');

        // Assert deletion
        $collection->delete('B2');
        self::assertFalse($collection->has('B2'), 'cell should have been deleted');
        self::assertEquals(['A1', 'Z1', 'AA1'], $collection->getCoordinates(), 'cell list should still contains the A1,Z1 and A11 cells');

        // Assert update
        $cell2 = $sheet->getCell('A1');
        self::assertSame($sheet->getCellCollection(), $collection);
        self::assertSame($cell2, $collection->update($cell2), 'should update existing cell');

        $cell3 = $sheet->getCell('C3');
        self::assertSame($cell3, $collection->update($cell3), 'should silently add non-existing C3 cell');
        self::assertEquals(['A1', 'Z1', 'AA1', 'C3'], $collection->getCoordinates(), 'cell list should contains the C3 cell');
        $spreadsheet->disconnectWorksheets();
    }

    public function testCacheLastCell(): void
    {
        $workbook = new Spreadsheet();
        $cells = ['A1', 'A2'];
        $sheet = $workbook->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2);
        self::assertEquals($cells, $sheet->getCoordinates(), 'list should include last added cell');
        $workbook->disconnectWorksheets();
    }

    public function testCanGetCellAfterAnotherIsDeleted(): void
    {
        $workbook = new Spreadsheet();
        $sheet = $workbook->getActiveSheet();
        $collection = $sheet->getCellCollection();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 1);
        $collection->delete('A1');
        $sheet->setCellValue('A3', 1);
        self::assertNotNull($collection->get('A2'), 'should be able to get back the cell even when another cell was deleted while this one was the current one');
        $workbook->disconnectWorksheets();
    }

    public function testThrowsWhenCellCannotBeRetrievedFromCache(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $collection = $this->getMockBuilder(Cells::class)
            ->setConstructorArgs([
                new Worksheet(),
                Settings::useSimpleCacheVersion3() ? new Memory\SimpleCache3() : new Memory\SimpleCache1(),
            ])
            ->onlyMethods(['has'])
            ->getMock();

        $collection->method('has')
            ->willReturn(true);

        $collection->get('A2');
    }

    public function testThrowsWhenCellCannotBeStoredInCache(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $cache = $this->createMock(
            Settings::useSimpleCacheVersion3() ? Memory\SimpleCache3::class : Memory\SimpleCache1::class
        );
        $cell = $this->createMock(Cell::class);
        $cache->method('set')
            ->willReturn(false);

        $collection = new Cells(new Worksheet(), $cache);

        $collection->add('A1', $cell);
        $collection->add('A2', $cell);
    }

    public function testGetHighestColumn(): void
    {
        $workbook = new Spreadsheet();
        $sheet = $workbook->getActiveSheet();
        $collection = $sheet->getCellCollection();

        // check for empty sheet
        self::assertEquals('A', $collection->getHighestColumn());
        self::assertEquals('A', $collection->getHighestColumn(1));

        // set a value and check again
        $sheet->getCell('C4')->setValue(1);
        self::assertEquals('C', $collection->getHighestColumn());
        self::assertEquals('A', $collection->getHighestColumn(1));
        self::assertEquals('C', $collection->getHighestColumn(4));
        $workbook->disconnectWorksheets();
    }

    public function testGetHighestColumnBad(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->expectExceptionMessage('Row number must be a positive integer');
        $workbook = new Spreadsheet();
        $sheet = $workbook->getActiveSheet();
        $collection = $sheet->getCellCollection();

        // check for empty sheet
        self::assertEquals('A', $collection->getHighestColumn());
        $collection->getHighestColumn(0);
        $workbook->disconnectWorksheets();
    }

    public function testRemoveRowBad(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->expectExceptionMessage('Row number must be a positive integer');
        $workbook = new Spreadsheet();
        $sheet = $workbook->getActiveSheet();
        $collection = $sheet->getCellCollection();
        $collection->removeRow(0);
        $workbook->disconnectWorksheets();
    }
}
