<?php

namespace PhpOffice\PhpSpreadsheetTests\Collection;

use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Collection\Memory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet;

class CellsTest extends \PHPUnit_Framework_TestCase
{
    public function testCollectionCell()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $collection = $sheet->getCellCollection();

        // Assert empty state
        $this->assertEquals([], $collection->getCoordinates(), 'cell list should be empty');
        $this->assertEquals([], $collection->getSortedCoordinates(), 'sorted cell list should be empty');
        $this->assertNull($collection->get('B2'), 'getting non-existing cell must return null');
        $this->assertFalse($collection->has('B2'), 'non-existing cell should be non-existent');

        // Add one cell
        $cell1 = $sheet->getCell('B2');
        $this->assertSame($cell1, $collection->add('B2', $cell1), 'adding a cell should return the cell');

        // Assert cell presence
        $this->assertEquals(['B2'], $collection->getCoordinates(), 'cell list should contains the cell');
        $this->assertEquals(['B2'], $collection->getSortedCoordinates(), 'sorted cell list contains the cell');
        $this->assertSame($cell1, $collection->get('B2'), 'should get exact same object');
        $this->assertTrue($collection->has('B2'), 'cell should exists');

        // Add a second cell
        $cell2 = $sheet->getCell('A1');
        $this->assertSame($cell2, $collection->add('A1', $cell2), 'adding a second cell should return the cell');
        $this->assertEquals(['B2', 'A1'], $collection->getCoordinates(), 'cell list should contains the cell');
        $this->assertEquals(['A1', 'B2'], $collection->getSortedCoordinates(), 'sorted cell list contains the cell');

        // Assert collection copy
        $sheet2 = $spreadsheet->createSheet();
        $collection2 = $collection->cloneCellCollection($sheet2);
        $this->assertTrue($collection2->has('A1'));
        $copiedCell2 = $collection2->get('A1');
        $this->assertNotSame($cell2, $copiedCell2, 'copied cell should not be the same object any more');
        $this->assertSame($collection2, $copiedCell2->getParent(), 'copied cell should be owned by the copied collection');
        $this->assertSame('A1', $copiedCell2->getCoordinate(), 'copied cell should keep attributes');

        // Assert deletion
        $collection->delete('B2');
        $this->assertFalse($collection->has('B2'), 'cell should have been deleted');
        $this->assertEquals(['A1'], $collection->getCoordinates(), 'cell list should contains the cell');

        // Assert update
        $cell2 = $sheet->getCell('A1');
        $this->assertSame($sheet->getCellCollection(), $collection);
        $this->assertSame($cell2, $collection->update($cell2), 'should update existing cell');

        $cell3 = $sheet->getCell('C3');
        $this->assertSame($cell3, $collection->update($cell3), 'should silently add non-existing cell');
        $this->assertEquals(['A1', 'C3'], $collection->getCoordinates(), 'cell list should contains the cell');
    }

    public function testCacheLastCell()
    {
        $workbook = new Spreadsheet();
        $cells = ['A1', 'A2'];
        $sheet = $workbook->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 2);
        $this->assertEquals($cells, $sheet->getCoordinates(), 'list should include last added cell');
    }

    public function testCanGetCellAfterAnotherIsDeleted()
    {
        $workbook = new Spreadsheet();
        $sheet = $workbook->getActiveSheet();
        $collection = $sheet->getCellCollection();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 1);
        $collection->delete('A1');
        $sheet->setCellValue('A3', 1);
        $this->assertNotNull($collection->get('A2'), 'should be able to get back the cell even when another cell was deleted while this one was the current one');
    }

    /**
     * @expectedException \PhpOffice\PhpSpreadsheet\Exception
     */
    public function testThrowsWhenCellCannotBeRetrievedFromCache()
    {
        $collection = $this->getMockBuilder(Cells::class)
            ->setConstructorArgs([new Worksheet(), new Memory()])
            ->setMethods(['has'])
            ->getMock();

        $collection->method('has')
            ->willReturn(true);

        $collection->get('A2');
    }

    /**
     * @expectedException \PhpOffice\PhpSpreadsheet\Exception
     */
    public function testThrowsWhenCellCannotBeStoredInCache()
    {
        $cache = $this->createMock(Memory::class);
        $cell = $this->createMock(Cell::class);
        $cache->method('set')
            ->willReturn(false);

        $collection = new Cells(new Worksheet(), $cache);

        $collection->add('A1', $cell);
        $collection->add('A2', $cell);
    }
}
