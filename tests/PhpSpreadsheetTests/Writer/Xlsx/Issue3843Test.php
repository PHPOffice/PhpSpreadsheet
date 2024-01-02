<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3843Test extends AbstractFunctional
{
    private const DIRECTORY = 'tests/data/Writer/XLSX/';
    private const TEMPLATE = self::DIRECTORY . 'issue.3843a.template.xlsx';
    private const IMAGE = self::DIRECTORY . 'issue.3843a.jpg';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::TEMPLATE;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<drawing r:id="rId2"/>', $data);
        }
    }

    public function testUnusedDrawingsInOriginal(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::TEMPLATE);
        $sheet = $spreadsheet->getActiveSheet();
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(0, $drawings);
        $drawing = new Drawing();
        $drawing->setName('TestDrawing');
        $drawing->setPath(self::IMAGE);
        $drawing->setCoordinates('A2');
        $drawing->setWidth(100);
        $drawing->setHeight(100);
        $drawing->setResizeProportional(false);
        $drawing->setWorksheet($sheet);
        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $drawings = $reloadedSpreadsheet->getActiveSheet()->getDrawingCollection();
        self::assertCount(1, $drawings);
        $drawing = $drawings[0];
        self::assertSame('TestDrawing', $drawing?->getName());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
