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
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load(self::TEMPLATE);
        $unparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $spreadsheet->getActiveSheet()->getCodeName();
        self::assertIsArray($unparsedData['sheets']);
        self::assertArrayHasKey($codeName, $unparsedData['sheets']);
        self::assertIsArray($unparsedData['sheets'][$codeName]);
        self::assertArrayHasKey('Drawings', $unparsedData['sheets'][$codeName], 'Original file should have drawings');
        self::assertIsArray($unparsedData['sheets'][$codeName]['Drawings']);
        $drawings = $unparsedData['sheets'][$codeName]['Drawings'];
        $drawingXml = reset($drawings);
        self::assertIsString($drawingXml);
        self::assertStringContainsString('<xdr:sp', $drawingXml, 'Original file should contain shape');
        $spreadsheet->disconnectWorksheets();

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

    /**
     * Test that the drawingPassThroughEnabled flag is correctly set in unparsedLoadedData.
     * This verifies the Reader sets the flag and the Writer's getPassThroughDrawingXml checks it.
     */
    public function testDrawingPassThroughEnabledFlagIsSetCorrectly(): void
    {
        // Test 1: Load WITHOUT pass-through (default)
        $reader = new XlsxReader();
        self::assertFalse($reader->getEnableDrawingPassThrough(), 'Pass-through should be disabled by default');
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();
        $unparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();

        // Verify that drawingPassThroughEnabled flag is NOT set when pass-through is disabled
        self::assertArrayHasKey('sheets', $unparsedData);
        self::assertIsArray($unparsedData['sheets']);

        // The sheet may exist in unparsedData (legacy empty drawings), but the flag should be absent or false
        if (isset($unparsedData['sheets'][$codeName])) {
            $sheetData = $unparsedData['sheets'][$codeName];
            self::assertIsArray($sheetData);
            $flag = $sheetData['drawingPassThroughEnabled'] ?? false;
            self::assertFalse($flag, 'drawingPassThroughEnabled should be false/absent when pass-through is disabled');
        }

        $spreadsheet->disconnectWorksheets();

        // Test 2: Load WITH pass-through enabled
        $reader2 = new XlsxReader();
        $reader2->setEnableDrawingPassThrough(true);
        self::assertTrue($reader2->getEnableDrawingPassThrough(), 'Pass-through should be enabled');
        $spreadsheet2 = $reader2->load(self::TEMPLATE);

        $sheet2 = $spreadsheet2->getActiveSheet();
        $unparsedData2 = $spreadsheet2->getUnparsedLoadedData();
        $codeName2 = $sheet2->getCodeName();

        // Verify that drawingPassThroughEnabled flag IS set when pass-through is enabled
        self::assertArrayHasKey('sheets', $unparsedData2);
        self::assertIsArray($unparsedData2['sheets']);
        self::assertArrayHasKey($codeName2, $unparsedData2['sheets']);
        self::assertIsArray($unparsedData2['sheets'][$codeName2]);
        self::assertArrayHasKey('drawingPassThroughEnabled', $unparsedData2['sheets'][$codeName2], 'drawingPassThroughEnabled flag should exist');
        self::assertTrue($unparsedData2['sheets'][$codeName2]['drawingPassThroughEnabled'], 'drawingPassThroughEnabled should be true when pass-through is enabled');

        // Verify that the drawing XML is also stored
        self::assertArrayHasKey('Drawings', $unparsedData2['sheets'][$codeName2]);
        self::assertIsArray($unparsedData2['sheets'][$codeName2]['Drawings']);
        self::assertNotEmpty($unparsedData2['sheets'][$codeName2]['Drawings'], 'Drawing XML should be stored when pass-through is enabled');

        $spreadsheet2->disconnectWorksheets();
    }

    /**
     * Test that VML drawings (used by comments) and DrawingML (used by shapes/images)
     * coexist without interference when pass-through is enabled.
     * This addresses the concern that comments use the drawings folder with VML files.
     * The template file already contains a comment in D1, so this test verifies that
     * existing comments are preserved AND new comments can be added.
     */
    public function testCommentsAndPassThroughCoexist(): void
    {
        // Load file with drawings (image + shape) and enable pass-through
        // Note: The template already contains a comment in D1
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();

        // Verify the existing comment is loaded
        $existingComment = $sheet->getComment('D1');
        $existingCommentText = $existingComment->getText()->getPlainText();
        self::assertNotEmpty($existingCommentText, 'Template should contain a comment in D1');

        // Add a new comment to the sheet
        $sheet->getComment('A1')->getText()->createText('Test comment with pass-through');
        $sheet->getComment('A1')->setAuthor('Test Author');

        // Save the file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify the file structure contains both VML (for comments) and DrawingML (for shapes)
        $zip = new ZipArchive();
        $zip->open($tempFile);

        // Check for VML drawing (used by comments)
        $vmlDrawing = $zip->getFromName('xl/drawings/vmlDrawing1.vml');
        self::assertNotFalse($vmlDrawing, 'VML drawing for comments should exist');
        self::assertStringContainsString('urn:schemas-microsoft-com:vml', $vmlDrawing, 'VML should contain VML namespace');

        // Check for DrawingML (used by shapes/images)
        $drawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        self::assertNotFalse($drawingXml, 'DrawingML for shapes/images should exist');
        self::assertStringContainsString('<xdr:sp', $drawingXml, 'DrawingML should contain shape (preserved by pass-through)');
        self::assertStringContainsString('<xdr:pic', $drawingXml, 'DrawingML should contain image');

        // Check for comments XML (should contain both existing and new comments)
        $commentsXml = $zip->getFromName('xl/comments1.xml');
        self::assertNotFalse($commentsXml, 'Comments XML should exist');
        self::assertStringContainsString('Test comment with pass-through', $commentsXml, 'New comment (A1) should be in comments XML');
        self::assertStringContainsString($existingCommentText, $commentsXml, 'Existing comment (D1) should be preserved in comments XML');

        $zip->close();
        unlink($tempFile);
    }
}
