<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class CopyXmlTest extends TestCase
{
    public function testCopyXml(): void
    {
        $infile = 'samples/templates/32readwriteRadarChart4.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($infile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('Charts');
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0] ?? null;
        self::assertInstanceOf(Chart::class, $chart);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writerChart = new XlsxWriter\Chart($writer);
        $data = $writerChart->writeChart($chart);
        //echo $data;
        self::assertStringContainsString('<c:date1904 val="0"/>', $data, 'From input even though same as default');
        self::assertStringContainsString('<c:lang val="en-US"/>', $data, 'From input, different from default');
        self::assertStringNotContainsString('view3D', $data, 'No empty view3D tag');
        self::assertStringContainsString('<a:bodyPr vertOverflow="overflow" horzOverflow="overflow" wrap="square" lIns="38100" tIns="19050" rIns="38100" bIns="19050" anchor="ctr">', $data, 'A couple of extra attributes');
        self::assertStringContainsString('<c:pageMargins b="0.75" l="0.25" r="0.25" t="0.75" header="0.3" footer="0.3"/>', $data, 'Some different values plus re-shuffling');
        self::assertStringContainsString('<c:pageSetup paperSize="9" orientation="portrait"/>', $data, 'An extra attribute');

        $spreadsheet->disconnectWorksheets();
    }
}
