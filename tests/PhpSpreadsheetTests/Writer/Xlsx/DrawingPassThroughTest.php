<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use ZipArchive;

class DrawingPassThroughTest extends AbstractFunctional
{
    private const DIRECTORY = 'tests/data/Writer/XLSX/';
    private const TEMPLATE = self::DIRECTORY . 'issue.4037.xlsx';

    /**
     * Test that unsupported drawing elements (shapes, textboxes) are preserved
     * when pass-through is enabled and no drawings are modified.
     */
    public function testDrawingPassThroughPreservesUnsupportedElements(): void
    {
        // Load with pass-through enabled
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();

        // Verify that drawing collection contains only the image (supported element)
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings, 'Drawing collection should contain only the image (supported element)');

        // Verify that unparsed data contains the original drawing XML with shapes
        $unparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();
        self::assertArrayHasKey('sheets', $unparsedData);
        self::assertIsArray($unparsedData['sheets']);
        self::assertArrayHasKey($codeName, $unparsedData['sheets']);
        self::assertIsArray($unparsedData['sheets'][$codeName]);
        self::assertArrayHasKey('Drawings', $unparsedData['sheets'][$codeName]);

        // Verify that the drawing XML contains shapes and textboxes
        self::assertIsArray($unparsedData['sheets'][$codeName]['Drawings']);
        $drawings = $unparsedData['sheets'][$codeName]['Drawings'];
        $originalDrawingXml = reset($drawings);
        self::assertIsString($originalDrawingXml);
        self::assertStringContainsString('<xdr:sp', $originalDrawingXml, 'Original XML should contain shape element');
        self::assertStringContainsString('<xdr:txBody>', $originalDrawingXml, 'Original XML should contain textbox element');

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify that the saved XLSX file contains shapes by reading the drawing XML directly
        $zip = new ZipArchive();
        $zip->open($tempFile);
        $drawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        $zip->close();
        unlink($tempFile);

        self::assertNotFalse($drawingXml, 'Drawing XML should exist in saved file');
        self::assertStringContainsString('<xdr:sp>', $drawingXml, 'Shapes should be preserved in saved file');
        self::assertStringContainsString('<xdr:txBody>', $drawingXml, 'Textboxes should be preserved in saved file');
    }

    /**
     * Test that WITHOUT Reader pass-through flag, shapes are NOT stored and are LOST.
     * This test uses a file with both an image (supported) and a shape (unsupported).
     */
    public function testWithoutReaderPassThroughShapesAreLost(): void
    {
        // First, verify that the original file contains a shape
        // Load WITH pass-through to check file contents
        $verifyReader = new XlsxReader();
        $verifyReader->setEnableDrawingPassThrough(true);
        $verifySpreadsheet = $verifyReader->load(self::TEMPLATE);
        $verifyUnparsedData = $verifySpreadsheet->getUnparsedLoadedData();
        $verifyCodeName = $verifySpreadsheet->getActiveSheet()->getCodeName();
        self::assertIsArray($verifyUnparsedData['sheets']);
        self::assertArrayHasKey($verifyCodeName, $verifyUnparsedData['sheets']);
        self::assertIsArray($verifyUnparsedData['sheets'][$verifyCodeName]);
        self::assertArrayHasKey('Drawings', $verifyUnparsedData['sheets'][$verifyCodeName], 'Original file should have drawings');
        self::assertIsArray($verifyUnparsedData['sheets'][$verifyCodeName]['Drawings']);
        $verifyDrawings = $verifyUnparsedData['sheets'][$verifyCodeName]['Drawings'];
        $verifyDrawingXml = reset($verifyDrawings);
        self::assertIsString($verifyDrawingXml);
        self::assertStringContainsString('<xdr:sp', $verifyDrawingXml, 'Original file should contain a shape');
        self::assertStringContainsString('<xdr:txBody>', $verifyDrawingXml, 'Original file should contain a textbox');
        $verifySpreadsheet->disconnectWorksheets();

        // Now test: Load WITHOUT Reader pass-through (XML not stored)
        $reader = new XlsxReader();
        // Don't enable pass-through!
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();

        // Verify that image is in collection (supported element)
        $drawings = $sheet->getDrawingCollection();
        self::assertGreaterThan(0, count($drawings), 'Drawing collection should contain the image');

        // Verify that shape XML is NOT stored (because pass-through disabled)
        $unparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();
        self::assertIsArray($unparsedData['sheets']);
        $sheetData = $unparsedData['sheets'][$codeName] ?? [];
        self::assertArrayNotHasKey('Drawings', $sheetData, 'Drawings should NOT be stored without Reader pass-through flag');

        // Save to file
        $writer = new XlsxWriter($spreadsheet);
        $tempFile = File::temporaryFilename();
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify that shape is LOST by reading the drawing XML directly
        $zip = new ZipArchive();
        $zip->open($tempFile);
        $drawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        $zip->close();
        unlink($tempFile);

        // The saved file should have drawing XML (for the image) but NOT the shape or textbox
        self::assertNotFalse($drawingXml, 'Drawing XML should exist (for the image)');
        self::assertStringNotContainsString('<xdr:sp>', $drawingXml, 'Shape should be lost without Reader pass-through');
        self::assertStringNotContainsString('<xdr:txBody>', $drawingXml, 'Textbox should be lost without Reader pass-through');
    }

    /**
     * Test that pass-through preserves drawings when a comment is deleted.
     * Comments are independent from drawings, so deleting a comment should not affect drawings.
     */
    public function testDrawingPassThroughWithCommentDeletion(): void
    {
        // Load with pass-through enabled
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();

        // Verify that image is in collection
        $drawings = $sheet->getDrawingCollection();
        self::assertGreaterThan(0, count($drawings), 'Drawing collection should contain the image');

        // Verify that shapes and textboxes are in unparsed data
        $unparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();
        self::assertIsArray($unparsedData['sheets']);
        self::assertArrayHasKey($codeName, $unparsedData['sheets']);
        self::assertIsArray($unparsedData['sheets'][$codeName]);
        self::assertArrayHasKey('Drawings', $unparsedData['sheets'][$codeName]);
        self::assertIsArray($unparsedData['sheets'][$codeName]['Drawings']);
        $drawings = $unparsedData['sheets'][$codeName]['Drawings'];
        $originalDrawingXml = reset($drawings);
        self::assertIsString($originalDrawingXml);
        self::assertStringContainsString('<xdr:sp', $originalDrawingXml, 'Original XML should contain shape');
        self::assertStringContainsString('<xdr:txBody>', $originalDrawingXml, 'Original XML should contain textbox');

        // Verify that a comment exists and delete it
        $comments = $sheet->getComments();
        self::assertGreaterThan(0, count($comments), 'Original file should have at least one comment');
        $firstCommentCell = array_key_first($comments);
        self::assertIsString($firstCommentCell, 'Comment cell should be a string');
        $originalCommentText = $sheet->getComment($firstCommentCell)->getText()->getPlainText();
        self::assertNotEmpty($originalCommentText, 'Comment should have text');

        // Delete the comment
        $sheet->removeComment($firstCommentCell);

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify that shapes are still present and comment was deleted
        $zip = new ZipArchive();
        $zip->open($tempFile);
        $drawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        $commentsXml = $zip->getFromName('xl/comments1.xml');
        $zip->close();
        unlink($tempFile);

        self::assertNotFalse($drawingXml, 'Drawing XML should exist in saved file');
        self::assertStringContainsString('<xdr:sp>', $drawingXml, 'Shapes should be preserved after comment deletion');
        self::assertStringContainsString('<xdr:txBody>', $drawingXml, 'Textboxes should be preserved after comment deletion');

        // Verify that comment was deleted (comments XML should not exist or not contain the original comment)
        if ($commentsXml !== false) {
            self::assertStringNotContainsString($originalCommentText, $commentsXml, 'Original comment text should be deleted');
        }
    }

    /**
     * Test that WITH pass-through, drawing modifications are NOT applied.
     * When pass-through is enabled, the Writer uses the stored XML instead of regenerating,
     * so programmatic changes to drawings are ignored.
     */
    public function testWithPassThroughDrawingModificationsAreIgnored(): void
    {
        // Load WITH pass-through
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();

        // Verify that image is in collection
        $drawings = $sheet->getDrawingCollection();
        self::assertGreaterThan(0, count($drawings), 'Drawing collection should contain the image');

        // Modify the drawing (change description)
        $drawing = null;
        foreach ($drawings as $d) {
            $drawing = $d;

            break;
        }
        self::assertNotNull($drawing, 'Should have at least one drawing');

        $originalDescription = $drawing->getDescription();
        $newDescription = 'Modified description by test';
        $drawing->setDescription($newDescription);
        self::assertNotSame($originalDescription, $newDescription, 'Description should be different');

        // Save to file (with pass-through, Writer uses stored XML, modifications ignored)
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Reload and verify that the modification was NOT applied (original description preserved)
        $reloadReader = new XlsxReader();
        $reloadedSpreadsheet = $reloadReader->load($tempFile);
        unlink($tempFile);

        $reloadedDrawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertGreaterThan(0, count($reloadedDrawings), 'Reloaded file should have drawings');

        $reloadedDrawing = null;
        foreach ($reloadedDrawings as $d) {
            $reloadedDrawing = $d;

            break;
        }
        self::assertNotNull($reloadedDrawing, 'Should have at least one reloaded drawing');
        self::assertSame($originalDescription, $reloadedDrawing->getDescription(), 'Original description should be preserved (modification ignored with pass-through)');
        self::assertNotSame($newDescription, $reloadedDrawing->getDescription(), 'Modified description should NOT be applied with pass-through');

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * Test that pass-through preserves drawings when columns are inserted,
     * but coordinates are NOT adjusted.
     */
    public function testDrawingPassThroughWithColumnInsertion(): void
    {
        // Load with pass-through enabled
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();

        // Get original drawing coordinates from XML
        $originalUnparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();
        self::assertIsArray($originalUnparsedData['sheets']);
        self::assertArrayHasKey($codeName, $originalUnparsedData['sheets']);
        self::assertIsArray($originalUnparsedData['sheets'][$codeName]);
        self::assertArrayHasKey('Drawings', $originalUnparsedData['sheets'][$codeName]);
        self::assertIsArray($originalUnparsedData['sheets'][$codeName]['Drawings']);
        $originalDrawings = $originalUnparsedData['sheets'][$codeName]['Drawings'];
        $originalDrawingXml = reset($originalDrawings);
        self::assertIsString($originalDrawingXml);

        // Extract original column coordinate
        preg_match('/<xdr:col>(\d+)<\/xdr:col>/', $originalDrawingXml, $originalColMatches);
        $originalCol = $originalColMatches[1] ?? null;
        self::assertNotNull($originalCol, 'Original drawing should have column coordinate');

        // Insert a column before B (which should shift drawings at B or later)
        $sheet->insertNewColumnBefore('B', 1);

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Read the drawing XML directly from the saved file
        $zip = new ZipArchive();
        $zip->open($tempFile);
        $reloadedDrawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        $zip->close();
        unlink($tempFile);

        self::assertNotFalse($reloadedDrawingXml, 'Drawing XML should exist in saved file');

        // Extract reloaded column coordinate
        preg_match('/<xdr:col>(\d+)<\/xdr:col>/', $reloadedDrawingXml, $reloadedColMatches);
        $reloadedCol = $reloadedColMatches[1] ?? null;

        // Coordinates are NOT adjusted
        // The column coordinate should remain the same (not shifted)
        self::assertSame($originalCol, $reloadedCol, 'Drawing column coordinate should NOT be adjusted');
    }

    /**
     * Test that pass-through preserves drawings when rows are deleted,
     * but coordinates are NOT adjusted.
     */
    public function testDrawingPassThroughWithRowDeletion(): void
    {
        // Load with pass-through enabled
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();

        // Get original drawing coordinates from XML
        $originalUnparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();
        self::assertIsArray($originalUnparsedData['sheets']);
        self::assertArrayHasKey($codeName, $originalUnparsedData['sheets']);
        self::assertIsArray($originalUnparsedData['sheets'][$codeName]);
        self::assertArrayHasKey('Drawings', $originalUnparsedData['sheets'][$codeName]);
        self::assertIsArray($originalUnparsedData['sheets'][$codeName]['Drawings']);
        $originalDrawings = $originalUnparsedData['sheets'][$codeName]['Drawings'];
        $originalDrawingXml = reset($originalDrawings);
        self::assertIsString($originalDrawingXml);

        // Extract original row coordinate
        preg_match('/<xdr:row>(\d+)<\/xdr:row>/', $originalDrawingXml, $originalRowMatches);
        $originalRow = $originalRowMatches[1] ?? null;
        self::assertNotNull($originalRow, 'Original drawing should have row coordinate');

        // Delete row 1 (which should shift drawings at row 2 or later)
        $sheet->removeRow(1, 1);

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Read the drawing XML directly from the saved file
        $zip = new ZipArchive();
        $zip->open($tempFile);
        $reloadedDrawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        $zip->close();
        unlink($tempFile);

        self::assertNotFalse($reloadedDrawingXml, 'Drawing XML should exist in saved file');

        // Extract reloaded row coordinate
        preg_match('/<xdr:row>(\d+)<\/xdr:row>/', $reloadedDrawingXml, $reloadedRowMatches);
        $reloadedRow = $reloadedRowMatches[1] ?? null;

        // Coordinates are NOT adjusted
        // The row coordinate should remain the same (not shifted)
        self::assertSame($originalRow, $reloadedRow, 'Drawing row coordinate should NOT be adjusted after row deletion');
    }

    public function testDrawingPassThroughGetterSetter(): void
    {
        // Test Reader getter/setter
        $reader = new XlsxReader();

        // Default should be false
        self::assertFalse($reader->getEnableDrawingPassThrough());

        // Enable pass-through
        $result = $reader->setEnableDrawingPassThrough(true);
        self::assertInstanceOf(XlsxReader::class, $result);
        self::assertTrue($reader->getEnableDrawingPassThrough());

        // Disable pass-through
        $reader->setEnableDrawingPassThrough(false);
        self::assertFalse($reader->getEnableDrawingPassThrough());
    }
}
