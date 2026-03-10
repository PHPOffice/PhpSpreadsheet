<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the optimized ReferenceHelper cell iteration using
 * Cells::getCoordinatesInRange() and Cells::getCoordinatesOutsideRange().
 */
class ReferenceHelper6Test extends TestCase
{
    /**
     * Test that inserting rows correctly updates formula references.
     */
    public function testInsertRowsUpdatesFormulaReferences(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set up data and formulas
        $sheet->setCellValue('A1', 10);
        $sheet->setCellValue('A2', 20);
        $sheet->setCellValue('A3', 30);
        $sheet->setCellValue('A4', '=SUM(A1:A3)');
        $sheet->setCellValue('B1', '=A1*2');
        $sheet->setCellValue('B2', '=A2*2');
        $sheet->setCellValue('B3', '=A3*2');

        // Insert 2 rows before row 2
        $sheet->insertNewRowBefore(2, 2);

        // A1 should remain at A1 (before insertion point)
        self::assertSame(10, $sheet->getCell('A1')->getValue());

        // Original A2 (value 20) should now be at A4
        self::assertSame(20, $sheet->getCell('A4')->getValue());

        // Original A3 (value 30) should now be at A5
        self::assertSame(30, $sheet->getCell('A5')->getValue());

        // Original A4 (=SUM(A1:A3)) should now be at A6 with updated reference
        self::assertSame('=SUM(A1:A5)', $sheet->getCell('A6')->getValue());
        self::assertEquals(60, $sheet->getCell('A6')->getCalculatedValue());

        // Formula B1 (=A1*2) stays at B1 (before insertion for column, but row >= 1 so it moves)
        // Actually B1 is at row 1 which is before row 2, so it stays
        self::assertSame('=A1*2', $sheet->getCell('B1')->getValue());
        self::assertSame(20, $sheet->getCell('B1')->getCalculatedValue());

        // Original B2 (=A2*2) should now be at B4 with updated reference =A4*2
        self::assertSame('=A4*2', $sheet->getCell('B4')->getValue());
        self::assertSame(40, $sheet->getCell('B4')->getCalculatedValue());

        // Original B3 (=A3*2) should now be at B5 with updated reference =A5*2
        self::assertSame('=A5*2', $sheet->getCell('B5')->getValue());
        self::assertSame(60, $sheet->getCell('B5')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test that inserting columns correctly updates formula references.
     */
    public function testInsertColumnsUpdatesFormulaReferences(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set up data and formulas
        $sheet->setCellValue('A1', 10);
        $sheet->setCellValue('B1', 20);
        $sheet->setCellValue('C1', 30);
        $sheet->setCellValue('D1', '=SUM(A1:C1)');
        $sheet->setCellValue('A2', '=A1+1');
        $sheet->setCellValue('B2', '=B1+1');
        $sheet->setCellValue('C2', '=C1+1');

        // Insert 2 columns before column B
        $sheet->insertNewColumnBefore('B', 2);

        // A1 should remain at A1
        self::assertSame(10, $sheet->getCell('A1')->getValue());

        // Original B1 (value 20) should now be at D1
        self::assertSame(20, $sheet->getCell('D1')->getValue());

        // Original C1 (value 30) should now be at E1
        self::assertSame(30, $sheet->getCell('E1')->getValue());

        // Original D1 (=SUM(A1:C1)) should now be at F1 with updated reference
        self::assertSame('=SUM(A1:E1)', $sheet->getCell('F1')->getValue());
        self::assertEquals(60, $sheet->getCell('F1')->getCalculatedValue());

        // Formula A2 (=A1+1) stays at A2 (column A is before B)
        self::assertSame('=A1+1', $sheet->getCell('A2')->getValue());
        self::assertSame(11, $sheet->getCell('A2')->getCalculatedValue());

        // Original B2 (=B1+1) should now be at D2 with updated reference =D1+1
        self::assertSame('=D1+1', $sheet->getCell('D2')->getValue());
        self::assertSame(21, $sheet->getCell('D2')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test that cell values and formulas are preserved correctly after insertion.
     */
    public function testCellValuesPreservedAfterInsertion(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set up various data types
        $sheet->setCellValue('A1', 'text');
        $sheet->setCellValue('A2', 42);
        $sheet->setCellValue('A3', 3.14);
        $sheet->setCellValue('A4', true);
        $sheet->setCellValue('A5', '=1+1');
        $sheet->setCellValue('B1', 'other');

        // Insert 1 row before row 3
        $sheet->insertNewRowBefore(3, 1);

        // Cells before insertion point are unchanged
        self::assertSame('text', $sheet->getCell('A1')->getValue());
        self::assertSame(42, $sheet->getCell('A2')->getValue());

        // Cells at/after insertion point are shifted down
        self::assertSame(3.14, $sheet->getCell('A4')->getValue());
        self::assertTrue($sheet->getCell('A5')->getValue());
        self::assertSame('=1+1', $sheet->getCell('A6')->getValue());
        self::assertSame(2, $sheet->getCell('A6')->getCalculatedValue());

        // Other column cells
        self::assertSame('other', $sheet->getCell('B1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test edge case: inserting rows at row 1 (the very beginning).
     */
    public function testInsertRowsAtRow1(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 100);
        $sheet->setCellValue('A2', 200);
        $sheet->setCellValue('A3', '=A1+A2');

        // Insert 3 rows at the very beginning
        $sheet->insertNewRowBefore(1, 3);

        // All cells should be shifted down by 3
        self::assertSame(100, $sheet->getCell('A4')->getValue());
        self::assertSame(200, $sheet->getCell('A5')->getValue());
        self::assertSame('=A4+A5', $sheet->getCell('A6')->getValue());
        self::assertSame(300, $sheet->getCell('A6')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test edge case: inserting a column at the last used column.
     */
    public function testInsertColumnAtLastColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('B1', 2);
        $sheet->setCellValue('C1', '=A1+B1');

        // Insert a column before C (the last used column)
        $sheet->insertNewColumnBefore('C', 1);

        // A1 and B1 should be unchanged
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        self::assertSame(2, $sheet->getCell('B1')->getValue());

        // C1 should be empty (newly inserted)
        self::assertNull($sheet->getCell('C1')->getValue());

        // Original C1 formula should now be at D1 with the same references
        self::assertSame('=A1+B1', $sheet->getCell('D1')->getValue());
        self::assertSame(3, $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test formulas in cells before the insertion point that reference
     * cells in/after the insertion range are properly updated.
     */
    public function testFormulaReferencesUpdatedBeforeInsertionPoint(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Formula in A1 references cells that will be shifted
        $sheet->setCellValue('A1', '=B3+B4');
        $sheet->setCellValue('B3', 10);
        $sheet->setCellValue('B4', 20);

        // Insert 2 rows before row 3
        $sheet->insertNewRowBefore(3, 2);

        // A1 should stay at A1 but formula should be updated
        self::assertSame('=B5+B6', $sheet->getCell('A1')->getValue());
        self::assertSame(30, $sheet->getCell('A1')->getCalculatedValue());

        // Values should be at new positions
        self::assertSame(10, $sheet->getCell('B5')->getValue());
        self::assertSame(20, $sheet->getCell('B6')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test the Cells::getCoordinatesInRange() method directly.
     */
    public function testGetCoordinatesInRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('B1', 2);
        $sheet->setCellValue('C1', 3);
        $sheet->setCellValue('A2', 4);
        $sheet->setCellValue('B2', 5);
        $sheet->setCellValue('C2', 6);
        $sheet->setCellValue('A3', 7);
        $sheet->setCellValue('B3', 8);
        $sheet->setCellValue('C3', 9);

        $cells = $sheet->getCellCollection();

        // Get cells at or after row 2, column B (index 2)
        $result = $cells->getCoordinatesInRange(2, 2);

        // Should contain: B2, C2, B3, C3
        self::assertCount(4, $result);
        self::assertContains('B2', $result);
        self::assertContains('C2', $result);
        self::assertContains('B3', $result);
        self::assertContains('C3', $result);

        // Should NOT contain: A1, B1, C1, A2, A3
        self::assertNotContains('A1', $result);
        self::assertNotContains('B1', $result);
        self::assertNotContains('C1', $result);
        self::assertNotContains('A2', $result);
        self::assertNotContains('A3', $result);

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test the Cells::getCoordinatesOutsideRange() method directly.
     */
    public function testGetCoordinatesOutsideRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('B1', 2);
        $sheet->setCellValue('C1', 3);
        $sheet->setCellValue('A2', 4);
        $sheet->setCellValue('B2', 5);
        $sheet->setCellValue('C2', 6);
        $sheet->setCellValue('A3', 7);
        $sheet->setCellValue('B3', 8);
        $sheet->setCellValue('C3', 9);

        $cells = $sheet->getCellCollection();

        // Get cells outside row 2, column B (index 2)
        $result = $cells->getCoordinatesOutsideRange(2, 2);

        // Should contain: A1, B1, C1, A2, A3 (those with row < 2 OR col < 2)
        self::assertCount(5, $result);
        self::assertContains('A1', $result);
        self::assertContains('B1', $result);
        self::assertContains('C1', $result);
        self::assertContains('A2', $result);
        self::assertContains('A3', $result);

        // Should NOT contain: B2, C2, B3, C3
        self::assertNotContains('B2', $result);
        self::assertNotContains('C2', $result);
        self::assertNotContains('B3', $result);
        self::assertNotContains('C3', $result);

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test that getCoordinatesInRange with row 1 and column 1 returns all cells.
     */
    public function testGetCoordinatesInRangeFromOrigin(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('B2', 2);
        $sheet->setCellValue('C3', 3);

        $cells = $sheet->getCellCollection();

        // All cells are at row >= 1 and col >= 1
        $result = $cells->getCoordinatesInRange(1, 1);
        self::assertCount(3, $result);

        // Complement should be empty
        $outside = $cells->getCoordinatesOutsideRange(1, 1);
        self::assertCount(0, $outside);

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test inserting rows in a worksheet with formulas that span multiple
     * columns and rows, verifying everything stays consistent.
     */
    public function testInsertRowsWithCrossReferenceFormulas(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Build a small grid with cross-reference formulas
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('B1', 2);
        $sheet->setCellValue('A2', 3);
        $sheet->setCellValue('B2', 4);
        $sheet->setCellValue('C1', '=A1+B1');  // = 3
        $sheet->setCellValue('C2', '=A2+B2');  // = 7
        $sheet->setCellValue('A3', '=SUM(A1:A2)');  // = 4
        $sheet->setCellValue('B3', '=SUM(B1:B2)');  // = 6
        $sheet->setCellValue('C3', '=SUM(C1:C2)');  // = 10

        // Insert 1 row before row 2
        $sheet->insertNewRowBefore(2, 1);

        // Row 1 stays
        self::assertSame(1, $sheet->getCell('A1')->getValue());
        self::assertSame(2, $sheet->getCell('B1')->getValue());
        self::assertSame('=A1+B1', $sheet->getCell('C1')->getValue());

        // Original row 2 is now row 3
        self::assertSame(3, $sheet->getCell('A3')->getValue());
        self::assertSame(4, $sheet->getCell('B3')->getValue());
        self::assertSame('=A3+B3', $sheet->getCell('C3')->getValue());

        // Original row 3 (summary) is now row 4, references updated
        self::assertSame('=SUM(A1:A3)', $sheet->getCell('A4')->getValue());
        self::assertSame('=SUM(B1:B3)', $sheet->getCell('B4')->getValue());
        self::assertSame('=SUM(C1:C3)', $sheet->getCell('C4')->getValue());

        // Verify calculated values
        self::assertSame(3, $sheet->getCell('C1')->getCalculatedValue());
        self::assertSame(7, $sheet->getCell('C3')->getCalculatedValue());
        self::assertEquals(4, $sheet->getCell('A4')->getCalculatedValue());
        self::assertEquals(6, $sheet->getCell('B4')->getCalculatedValue());
        self::assertEquals(10, $sheet->getCell('C4')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test inserting a column at column A (the very beginning).
     */
    public function testInsertColumnAtColumnA(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 5);
        $sheet->setCellValue('B1', 10);
        $sheet->setCellValue('C1', '=A1+B1');

        // Insert 1 column before A
        $sheet->insertNewColumnBefore('A', 1);

        // All cells shift right by 1
        self::assertSame(5, $sheet->getCell('B1')->getValue());
        self::assertSame(10, $sheet->getCell('C1')->getValue());
        self::assertSame('=B1+C1', $sheet->getCell('D1')->getValue());
        self::assertSame(15, $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test removing rows preserves formulas and values correctly.
     */
    public function testRemoveRowsWithFormulas(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 10);
        $sheet->setCellValue('A2', 20);
        $sheet->setCellValue('A3', 30);
        $sheet->setCellValue('A4', 40);
        $sheet->setCellValue('A5', '=SUM(A1:A4)');

        // Remove row 2
        $sheet->removeRow(2, 1);

        // A1 stays
        self::assertSame(10, $sheet->getCell('A1')->getValue());
        // A3 shifted to A2
        self::assertSame(30, $sheet->getCell('A2')->getValue());
        // A4 shifted to A3
        self::assertSame(40, $sheet->getCell('A3')->getValue());
        // A5 shifted to A4 with updated range
        self::assertSame('=SUM(A1:A3)', $sheet->getCell('A4')->getValue());
        self::assertEquals(80, $sheet->getCell('A4')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test removing columns preserves formulas and values correctly.
     */
    public function testRemoveColumnsWithFormulas(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 10);
        $sheet->setCellValue('B1', 20);
        $sheet->setCellValue('C1', 30);
        $sheet->setCellValue('D1', 40);
        $sheet->setCellValue('E1', '=SUM(A1:D1)');

        // Remove column B
        $sheet->removeColumn('B', 1);

        // A1 stays
        self::assertSame(10, $sheet->getCell('A1')->getValue());
        // C1 shifted to B1
        self::assertSame(30, $sheet->getCell('B1')->getValue());
        // D1 shifted to C1
        self::assertSame(40, $sheet->getCell('C1')->getValue());
        // E1 shifted to D1 with updated range
        self::assertSame('=SUM(A1:C1)', $sheet->getCell('D1')->getValue());
        self::assertEquals(80, $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test inserting at a high column letter (XFD area is max, but let's test a middle-high one).
     */
    public function testInsertColumnAtHighColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Use columns around Z/AA boundary
        $sheet->setCellValue('Y1', 1);
        $sheet->setCellValue('Z1', 2);
        $sheet->setCellValue('AA1', 3);
        $sheet->setCellValue('AB1', '=Y1+Z1+AA1');

        // Insert 1 column before Z
        $sheet->insertNewColumnBefore('Z', 1);

        // Y1 stays (before insertion)
        self::assertSame(1, $sheet->getCell('Y1')->getValue());
        // Z1 is now empty (inserted)
        // Original Z1 (=2) is now AA1
        self::assertSame(2, $sheet->getCell('AA1')->getValue());
        // Original AA1 (=3) is now AB1
        self::assertSame(3, $sheet->getCell('AB1')->getValue());
        // Original AB1 formula is now AC1 with updated references
        self::assertSame('=Y1+AA1+AB1', $sheet->getCell('AC1')->getValue());
        self::assertSame(6, $sheet->getCell('AC1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }
}
