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

    /**
     * Test that pass-through preserves SVG images and their relationships.
     * Excel stores SVG images with a PNG fallback using separate rIds.
     * Without proper pass-through of relationships and media files, the rIds
     * become misaligned and images break.
     *
     * File structure of merge.excel.xlsx:
     * - xl/media/image1.png (PNG fallback for SVG)
     * - xl/media/image2.svg (SVG image)
     * - xl/media/image3.jpeg (JPEG image)
     * - Drawing relationships: rId1=PNG, rId2=SVG, rId3=JPEG
     */
    public function testDrawingPassThroughPreservesSvgImagesAndRelationships(): void
    {
        $template = self::DIRECTORY . 'merge.excel.xlsx';

        // Load with pass-through enabled
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load($template);

        $sheet = $spreadsheet->getActiveSheet();

        // Verify that drawing collection contains supported images (PNG and JPEG, not SVG)
        $drawings = $sheet->getDrawingCollection();
        self::assertGreaterThanOrEqual(1, count($drawings), 'Drawing collection should contain at least one supported image');

        // Verify that pass-through data is stored
        $unparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();
        self::assertArrayHasKey('sheets', $unparsedData);
        self::assertIsArray($unparsedData['sheets']);
        self::assertArrayHasKey($codeName, $unparsedData['sheets']);
        self::assertIsArray($unparsedData['sheets'][$codeName]);
        self::assertTrue($unparsedData['sheets'][$codeName]['drawingPassThroughEnabled'] ?? false);

        // Verify that drawing relationships are stored
        self::assertArrayHasKey('drawingRelationships', $unparsedData['sheets'][$codeName]);
        $relsXml = $unparsedData['sheets'][$codeName]['drawingRelationships'];
        self::assertIsString($relsXml);
        self::assertStringContainsString('image1.png', $relsXml, 'Relationships should reference PNG');
        self::assertStringContainsString('image2.svg', $relsXml, 'Relationships should reference SVG');
        self::assertStringContainsString('image3.jpeg', $relsXml, 'Relationships should reference JPEG');

        // Verify that media files paths are stored
        self::assertArrayHasKey('drawingMediaFiles', $unparsedData['sheets'][$codeName]);
        $mediaFiles = $unparsedData['sheets'][$codeName]['drawingMediaFiles'];
        self::assertIsArray($mediaFiles);
        self::assertGreaterThanOrEqual(3, count($mediaFiles), 'Should have at least 3 media files (PNG, SVG, JPEG)');

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify that the saved file preserves all images and relationships
        $zip = new ZipArchive();
        $zip->open($tempFile);

        // Check that all media files are present
        $pngContent = $zip->getFromName('xl/media/image1.png');
        $svgContent = $zip->getFromName('xl/media/image2.svg');
        $jpegContent = $zip->getFromName('xl/media/image3.jpeg');

        self::assertNotFalse($pngContent, 'PNG image should be preserved');
        self::assertNotFalse($svgContent, 'SVG image should be preserved');
        self::assertNotFalse($jpegContent, 'JPEG image should be preserved');

        // Check that drawing relationships are correct
        $drawingRels = $zip->getFromName('xl/drawings/_rels/drawing1.xml.rels');
        self::assertNotFalse($drawingRels, 'Drawing relationships file should exist');
        self::assertStringContainsString('image1.png', $drawingRels, 'Drawing rels should reference PNG');
        self::assertStringContainsString('image2.svg', $drawingRels, 'Drawing rels should reference SVG');
        self::assertStringContainsString('image3.jpeg', $drawingRels, 'Drawing rels should reference JPEG');

        // Check that drawing XML references are intact
        $drawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        self::assertNotFalse($drawingXml, 'Drawing XML should exist');

        // Verify SVG is referenced via svgBlip extension
        self::assertStringContainsString('svgBlip', $drawingXml, 'Drawing should contain SVG blip reference');

        $zip->close();
        unlink($tempFile);
    }

    /**
     * Test that WITHOUT pass-through, SVG images are lost due to rId misalignment.
     * This documents the expected behavior without the fix.
     */
    public function testWithoutPassThroughSvgImagesAreLost(): void
    {
        $template = self::DIRECTORY . 'merge.excel.xlsx';

        // Verify that the original file contains SVG
        $originalZip = new ZipArchive();
        $originalZip->open($template);
        $originalSvgContent = $originalZip->getFromName('xl/media/image2.svg');
        $originalZip->close();
        self::assertNotFalse($originalSvgContent, 'Original file should contain SVG');

        // Load WITHOUT pass-through
        $reader = new XlsxReader();
        // Don't enable pass-through
        $spreadsheet = $reader->load($template);

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify that SVG is lost
        $zip = new ZipArchive();
        $zip->open($tempFile);

        // SVG file should NOT be present (PhpSpreadsheet doesn't support SVG)
        $svgContent = $zip->getFromName('xl/media/image2.svg');
        self::assertFalse($svgContent, 'SVG image should NOT be present without pass-through');

        // PNG and JPEG should still be present (supported formats)
        // Note: filenames may be different as they are regenerated
        $mediaFiles = [];
        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $name = $zip->getNameIndex($i);
            if ($name !== false && str_starts_with($name, 'xl/media/')) {
                $mediaFiles[] = $name;
            }
        }

        // Should have some media files but not SVG
        self::assertNotEmpty($mediaFiles, 'Should have some media files');
        foreach ($mediaFiles as $file) {
            self::assertStringNotContainsString('.svg', $file, 'No SVG files should be present without pass-through');
        }

        $zip->close();
        unlink($tempFile);
    }

    /**
     * Test that pass-through preserves shapes (textboxes, rectangles, etc.)
     * in addition to images with SVG.
     */
    public function testDrawingPassThroughPreservesShapesAndSvg(): void
    {
        $template = self::DIRECTORY . 'merge.excel.xlsx';

        // Load with pass-through enabled
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load($template);

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify shapes are preserved
        $zip = new ZipArchive();
        $zip->open($tempFile);

        $drawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        self::assertNotFalse($drawingXml, 'Drawing XML should exist');

        // Check for shapes (xdr:sp elements)
        self::assertStringContainsString('<xdr:sp', $drawingXml, 'Shapes should be preserved');
        self::assertStringContainsString('<xdr:txBody>', $drawingXml, 'Textboxes should be preserved');

        // Check for images (xdr:pic elements)
        self::assertStringContainsString('<xdr:pic', $drawingXml, 'Images should be preserved');

        // Check for SVG reference
        self::assertStringContainsString('svgBlip', $drawingXml, 'SVG reference should be preserved');

        $zip->close();
        unlink($tempFile);
    }

    /**
     * Test that pass-through preserves grouped images.
     * Grouped images are not parsed into the drawing collection, so they would be lost
     * without proper pass-through of drawing XML, relationships, and media files.
     *
     * File structure of grouped_images.xlsx:
     * - Contains a group (xdr:grpSp) with 2 images inside
     * - xl/media/image1.png and xl/media/image2.png
     * - The images are referenced from within the group element
     */
    public function testDrawingPassThroughPreservesGroupedImages(): void
    {
        $template = self::DIRECTORY . 'grouped_images.xlsx';

        // Verify that the original file contains a group with images
        $originalZip = new ZipArchive();
        $originalZip->open($template);
        $originalDrawingXml = $originalZip->getFromName('xl/drawings/drawing1.xml');
        $originalRels = $originalZip->getFromName('xl/drawings/_rels/drawing1.xml.rels');
        $originalZip->close();

        self::assertNotFalse($originalDrawingXml, 'Original file should have drawing XML');
        self::assertStringContainsString('<xdr:grpSp>', $originalDrawingXml, 'Original file should contain a group');
        self::assertStringContainsString('<xdr:pic>', $originalDrawingXml, 'Original file should contain images inside group');
        self::assertNotFalse($originalRels, 'Original file should have drawing relationships');
        self::assertStringContainsString('image1.png', $originalRels, 'Original rels should reference image1.png');
        self::assertStringContainsString('image2.bmp', $originalRels, 'Original rels should reference image2.bmp');
        self::assertStringContainsString('image4.gif', $originalRels, 'Original rels should reference image4.gif');
        self::assertStringContainsString('image6.svg', $originalRels, 'Original rels should reference image6.svg');

        // Load with pass-through enabled
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(true);
        $spreadsheet = $reader->load($template);

        $sheet = $spreadsheet->getActiveSheet();

        // Verify that drawing collection is empty (grouped images are not parsed)
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(0, $drawings, 'Drawing collection should be empty (grouped images are not parsed)');

        // Verify that pass-through data is stored
        $unparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();
        self::assertArrayHasKey('sheets', $unparsedData);
        self::assertIsArray($unparsedData['sheets']);
        self::assertArrayHasKey($codeName, $unparsedData['sheets']);
        self::assertIsArray($unparsedData['sheets'][$codeName]);
        self::assertTrue($unparsedData['sheets'][$codeName]['drawingPassThroughEnabled'] ?? false, 'Pass-through should be enabled');

        // Verify that drawing relationships are stored
        self::assertArrayHasKey('drawingRelationships', $unparsedData['sheets'][$codeName], 'Drawing relationships should be stored');

        // Verify that media files paths are stored
        self::assertArrayHasKey('drawingMediaFiles', $unparsedData['sheets'][$codeName], 'Media files should be stored');
        $mediaFiles = $unparsedData['sheets'][$codeName]['drawingMediaFiles'];
        self::assertIsArray($mediaFiles);
        self::assertGreaterThanOrEqual(2, count($mediaFiles), 'Should have at least 2 media files');

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify that the saved file preserves the group and images
        $zip = new ZipArchive();
        $zip->open($tempFile);

        // Check that drawing XML contains the group
        $drawingXml = $zip->getFromName('xl/drawings/drawing1.xml');
        self::assertNotFalse($drawingXml, 'Drawing XML should exist in saved file');
        self::assertStringContainsString('<xdr:grpSp>', $drawingXml, 'Group should be preserved');
        self::assertStringContainsString('<xdr:pic>', $drawingXml, 'Images inside group should be preserved');

        // Check that drawing relationships are preserved
        $drawingRels = $zip->getFromName('xl/drawings/_rels/drawing1.xml.rels');
        self::assertNotFalse($drawingRels, 'Drawing relationships file should exist');
        self::assertStringContainsString('image1.png', $drawingRels, 'Drawing rels should reference image1.png');
        self::assertStringContainsString('image2.bmp', $drawingRels, 'Drawing rels should reference image2.bmp');
        self::assertStringContainsString('image4.gif', $drawingRels, 'Drawing rels should reference image4.gif');
        self::assertStringContainsString('image6.svg', $drawingRels, 'Drawing rels should reference image6.svg');

        // Check that media files are present
        $image1Content = $zip->getFromName('xl/media/image1.png');
        $image2Content = $zip->getFromName('xl/media/image2.bmp');
        $image4Content = $zip->getFromName('xl/media/image4.gif');
        $image6Content = $zip->getFromName('xl/media/image6.svg');
        self::assertNotFalse($image1Content, 'image1.png should be preserved');
        self::assertNotFalse($image2Content, 'image2.bmp should be preserved');
        self::assertNotFalse($image4Content, 'image4.gif should be preserved');
        self::assertNotFalse($image6Content, 'image6.svg should be preserved');

        // Check that Content_Types.xml contains all image extensions
        $contentTypes = $zip->getFromName('[Content_Types].xml');
        self::assertNotFalse($contentTypes, 'Content_Types.xml should exist');
        self::assertStringContainsString('image/png', $contentTypes, 'Content_Types should declare PNG mime type');
        self::assertStringContainsString('image/bmp', $contentTypes, 'Content_Types should declare BMP mime type');
        self::assertStringContainsString('image/gif', $contentTypes, 'Content_Types should declare GIF mime type');
        self::assertStringContainsString('image/svg+xml', $contentTypes, 'Content_Types should declare SVG mime type');

        $zip->close();
        unlink($tempFile);
    }

    /**
     * Test that WITHOUT pass-through, grouped images are lost.
     * This documents the expected behavior without the fix.
     */
    public function testWithoutPassThroughGroupedImagesAreLost(): void
    {
        $template = self::DIRECTORY . 'grouped_images.xlsx';

        // Verify that the original file contains grouped images
        $originalZip = new ZipArchive();
        $originalZip->open($template);
        $originalDrawingXml = $originalZip->getFromName('xl/drawings/drawing1.xml');
        $originalZip->close();
        self::assertStringContainsString('<xdr:grpSp>', $originalDrawingXml, 'Original file should contain a group');

        // Load WITHOUT pass-through
        $reader = new XlsxReader();
        // Don't enable pass-through
        $spreadsheet = $reader->load($template);

        $sheet = $spreadsheet->getActiveSheet();

        // Verify that drawing collection is empty (grouped images are not parsed)
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(0, $drawings, 'Drawing collection should be empty without pass-through too');

        // Save to file
        $tempFile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        // Verify that the group and images are lost
        $zip = new ZipArchive();
        $zip->open($tempFile);

        // Drawing XML should not exist or not contain the group
        $drawingXml = $zip->getFromName('xl/drawings/drawing1.xml');

        if ($drawingXml !== false) {
            // If drawing XML exists, it should NOT contain the group
            self::assertStringNotContainsString('<xdr:grpSp>', $drawingXml, 'Group should be lost without pass-through');
        }

        // Media files should NOT be present (nothing was in the drawing collection)
        $image1Content = $zip->getFromName('xl/media/image1.png');
        $image2Content = $zip->getFromName('xl/media/image2.bmp');
        $image4Content = $zip->getFromName('xl/media/image4.gif');
        $image6Content = $zip->getFromName('xl/media/image6.svg');
        self::assertFalse($image1Content, 'image1.png should NOT be present without pass-through');
        self::assertFalse($image2Content, 'image2.bmp should NOT be present without pass-through');
        self::assertFalse($image4Content, 'image4.gif should NOT be present without pass-through');
        self::assertFalse($image6Content, 'image6.svg should NOT be present without pass-through');

        $zip->close();
        unlink($tempFile);
    }
}
