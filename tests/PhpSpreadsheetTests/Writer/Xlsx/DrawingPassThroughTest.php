<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DrawingPassThroughTest extends AbstractFunctional
{
    private const DIRECTORY = 'tests/data/Writer/XLSX/';
    private const TEMPLATE = self::DIRECTORY . 'issue.3843a.template.xlsx';
    private const IMAGE = self::DIRECTORY . 'issue.3843a.jpg';

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

        // Verify that drawing collection is empty (unsupported elements not parsed)
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(0, $drawings, 'Drawing collection should be empty for unsupported elements');

        // Verify that unparsed data contains the original drawing XML
        $unparsedData = $spreadsheet->getUnparsedLoadedData();
        $codeName = $sheet->getCodeName();
        self::assertArrayHasKey('sheets', $unparsedData);
        self::assertIsArray($unparsedData['sheets']);
        self::assertArrayHasKey($codeName, $unparsedData['sheets']);
        self::assertIsArray($unparsedData['sheets'][$codeName]);
        self::assertArrayHasKey('Drawings', $unparsedData['sheets'][$codeName]);

        // Save and reload
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        // Verify that the drawing XML is still present after reload
        $reloadedUnparsedData = $reloadedSpreadsheet->getUnparsedLoadedData();
        $reloadedCodeName = $reloadedSpreadsheet->getActiveSheet()->getCodeName();
        self::assertArrayHasKey('sheets', $reloadedUnparsedData);
        self::assertIsArray($reloadedUnparsedData['sheets']);
        self::assertArrayHasKey($reloadedCodeName, $reloadedUnparsedData['sheets']);
        self::assertIsArray($reloadedUnparsedData['sheets'][$reloadedCodeName]);
        self::assertArrayHasKey('Drawings', $reloadedUnparsedData['sheets'][$reloadedCodeName]);

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * Test that pass-through is NOT used when drawings are added programmatically.
     */
    public function testDrawingPassThroughDisabledWhenDrawingsAdded(): void
    {
        // Load without pass-through (doesn't matter since we add drawings)
        $reader = new XlsxReader();
        $reader->setEnableDrawingPassThrough(false);
        $spreadsheet = $reader->load(self::TEMPLATE);

        $sheet = $spreadsheet->getActiveSheet();

        // Add a drawing programmatically
        $drawing = new Drawing();
        $drawing->setName('TestDrawing');
        $drawing->setPath(self::IMAGE);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);

        // Verify that drawing collection now has 1 element
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings, 'Drawing collection should contain the added drawing');

        // Save and reload
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        // Verify that the new drawing is present after reload
        $reloadedDrawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $reloadedDrawings, 'Reloaded spreadsheet should contain the added drawing');
        $firstDrawing = $reloadedDrawings[0] ?? null;
        self::assertNotNull($firstDrawing);
        self::assertSame('TestDrawing', $firstDrawing->getName());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testDrawingPassThroughGetterSetter(): void
    {
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
