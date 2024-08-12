<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3767Test extends AbstractFunctional
{
    // some weirdness with this file, including the fact that it has a
    // title ('Chart Title') which I cannot find anywhere in the xml.
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3767.xlsx';

    private string $tempfile = '';

    protected function tearDown(): void
    {
        if ($this->tempfile !== '') {
            unlink($this->tempfile);
            $this->tempfile = '';
        }
    }

    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testReadWithoutCharts(): void
    {
        $reader = new XlsxReader();
        //$this->readCharts($reader); // Commented out - don't want to read charts.
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(0, $charts);
        $this->tempfile = File::temporaryFileName();
        $writer = new XlsxWriter($spreadsheet);
        $this->writeCharts($writer);
        $writer->save($this->tempfile);
        $spreadsheet->disconnectWorksheets();
        $file = 'zip://';
        $file .= $this->tempfile;
        $file .= '#xl/worksheets/_rels/sheet1.xml.rels';
        $data = (string) file_get_contents($file);
        // PhpSpreadsheet still generates this target even though charts aren't included
        self::assertStringContainsString('Target="../drawings/drawing1.xml"', $data);
        $file = 'zip://';
        $file .= $this->tempfile;
        $file .= '#xl/drawings/drawing1.xml';
        $data = file_get_contents($file);
        self::assertSame('<xml></xml>', $data); // fake file because rels needs it
    }

    public function testReadWithCharts(): void
    {
        $reader = new XlsxReader();
        $this->readCharts($reader);
        $spreadsheet = $reader->load(self::$testbook);
        $xsheet = $spreadsheet->getActiveSheet();
        $xcharts = $xsheet->getChartCollection();
        self::assertCount(1, $xcharts);
        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $charts = $xsheet->getChartCollection();
        self::assertCount(1, $charts);
        // In Excel, a default title ('Chart Title') is shown.
        // I can't find that anywhere in the Xml.
        self::assertSame('', $charts[0]?->getTitle()?->getCaptionText());
        // Just test anything on the chart.
        self::assertSame($sheet->getCell('B2')->getValue(), $charts[0]->getPlotArea()?->getPlotGroup()[0]?->getPlotValues()[0]?->getDataValues()[0] ?? null);
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
