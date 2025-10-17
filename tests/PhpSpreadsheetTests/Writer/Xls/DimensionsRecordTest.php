<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PHPUnit\Framework\TestCase;

class DimensionsRecordTest extends TestCase
{
    /**
     * Test that DIMENSIONS record uses 0-based column indices.
     *
     * This test verifies that the BIFF8 DIMENSIONS record correctly uses 0-based
     * column indices. Prior to the fix, columnIndexFromString() returned 1-based
     * indices which were written directly to the DIMENSIONS record, causing issues
     * with old XLS parsers that expect 0-based indices per the BIFF8 specification.
     *
     * The DIMENSIONS record structure (BIFF8):
     * - Offset 0-3: Index to first used row
     * - Offset 4-7: Index to last used row + 1
     * - Offset 8-9: Index to first used column (0-based)
     * - Offset 10-11: Index to last used column + 1 (0-based)
     * - Offset 12-13: Not used
     */
    public function testDimensionsRecordUsesZeroBasedColumnIndices(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set values in columns A through D (should be indices 0-3 in 0-based)
        $sheet->setCellValue('A1', 'Column A');
        $sheet->setCellValue('B1', 'Column B');
        $sheet->setCellValue('C1', 'Column C');
        $sheet->setCellValue('D1', 'Column D');
        $sheet->setCellValue('A5', 'Row 5');

        // Write to XLS format
        $filename = tempnam(sys_get_temp_dir(), 'phpspreadsheet-test-');
        $writer = new Xls($spreadsheet);
        $writer->save($filename);
        $spreadsheet->disconnectWorksheets();

        // Read the binary file and find the DIMENSIONS record
        $fileContent = file_get_contents($filename);
        self::assertIsString($fileContent, 'Failed to read XLS file');
        unlink($filename);

        // Find the DIMENSIONS record: 0x0200 (2 bytes) + length 0x000E (2 bytes)
        $recordSignature = pack('v', 0x0200) . pack('v', 0x000E);
        $pos = strpos($fileContent, $recordSignature);

        self::assertIsInt($pos, 'DIMENSIONS record not found in XLS file');

        // Parse the DIMENSIONS record (skip 4-byte header)
        $dataPos = $pos + 4;
        $dimensionsData = substr($fileContent, $dataPos, 14);

        // Unpack DIMENSIONS record
        $data = unpack('VrwMic/VrwMac/vcolMic/vcolMac/vreserved', $dimensionsData);
        self::assertIsArray($data, 'Failed to unpack DIMENSIONS record');

        // Verify the values are correct (0-based for columns, 1-based for rows)
        // First used row is 1
        self::assertSame(1, $data['rwMic'], 'First row should be 1');

        // Last used row is 5, so rwMac should be 6 (last row + 1)
        self::assertSame(6, $data['rwMac'], 'Last row + 1 should be 6');

        // First column is A (0-based: 0)
        // BEFORE FIX: This would be 1 (because columnIndexFromString('A') returns 1)
        // AFTER FIX: This should be 0 (because we subtract 1)
        self::assertSame(0, $data['colMic'], 'First column should be 0 (0-based for column A)');

        // Last column is D (0-based index 3), so colMac should be 4 (last used + 1)
        // BEFORE FIX: This would be 5 (columnIndexFromString('D') = 4, not adjusted to 0-based)
        // AFTER FIX: This should be 4 (columnIndexFromString('D') - 1 = 3, + 1 = 4)
        self::assertSame(4, $data['colMac'], 'Last column + 1 should be 4 (0-based for column D + 1)');
    }

    /**
     * Test that DIMENSIONS record correctly handles columns near the BIFF8 limit.
     *
     * BIFF8 format supports columns up to IV (256 columns, 0-based index 0-255).
     * This test ensures that the lastColumnIndex is correctly capped at 255.
     */
    public function testDimensionsRecordCapsColumnIndexAt255(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set value in column IV (column 256, 0-based index 255)
        $sheet->setCellValue('IV1', 'Last BIFF8 Column');

        // Write to XLS format
        $filename = tempnam(sys_get_temp_dir(), 'phpspreadsheet-test-');
        $writer = new Xls($spreadsheet);
        $writer->save($filename);
        $spreadsheet->disconnectWorksheets();

        // Read the binary file and find the DIMENSIONS record
        $fileContent = file_get_contents($filename);
        self::assertIsString($fileContent, 'Failed to read XLS file');
        unlink($filename);

        // Find the DIMENSIONS record: 0x0200 (2 bytes) + length 0x000E (2 bytes)
        $recordSignature = pack('v', 0x0200) . pack('v', 0x000E);
        $pos = strpos($fileContent, $recordSignature);

        self::assertIsInt($pos, 'DIMENSIONS record not found in XLS file');

        // Parse the DIMENSIONS record (skip 4-byte header)
        $dataPos = $pos + 4;
        $dimensionsData = substr($fileContent, $dataPos, 14);

        // Unpack DIMENSIONS record
        $data = unpack('VrwMic/VrwMac/vcolMic/vcolMac/vreserved', $dimensionsData);
        self::assertIsArray($data, 'Failed to unpack DIMENSIONS record');

        // Last column should be capped at 256 (255 + 1 for "last used + 1")
        // The min(255, ...) ensures we don't exceed the BIFF8 limit
        self::assertLessThanOrEqual(256, $data['colMac'], 'Last column index should not exceed 256');
    }
}
