<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class WorksheetTest extends TestCase
{
    public function testFrozenPaneSelection()
    {
        // Create a dummy workbook with two worksheets
        $workbook = new Spreadsheet();
        $worksheet = $workbook->getActiveSheet();
        $worksheet->freezePane('A7', 'A24');
        $worksheet->setSelectedCells('F5');

        Settings::setLibXmlLoaderOptions(null); // reset to default options

        $resultFilename = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($workbook);
        $writer->save($resultFilename);

        try {
            $this->assertFileExists($resultFilename);

            $resultZip = new ZipArchive();
            $resultZip->open($resultFilename);
            $worksheetXmlStr = $resultZip->getFromName('xl/worksheets/sheet1.xml');
            $worksheetXml = simplexml_load_string($worksheetXmlStr);
            $this->assertInstanceOf('SimpleXMLElement', $worksheetXml);
            $sheetViewEl = $worksheetXml->sheetViews->sheetView;

            $paneEl = $sheetViewEl->pane;
            $this->assertEquals('6', (string) $paneEl['ySplit']);
            $this->assertEquals('A24', (string) $paneEl['topLeftCell']);
            $this->assertEquals('bottomLeft', (string) $paneEl['activePane']);
            $this->assertEquals('frozen', (string) $paneEl['state']);

            $selectionEl = $sheetViewEl->selection;
            $this->assertEquals('bottomLeft', (string) $selectionEl['pane']);
            $this->assertEquals('A24', (string) $selectionEl['activeCell']);
            $this->assertEquals('A24', (string) $selectionEl['sqref']);
        } finally {
            unlink($resultFilename);
        }
    }
}
