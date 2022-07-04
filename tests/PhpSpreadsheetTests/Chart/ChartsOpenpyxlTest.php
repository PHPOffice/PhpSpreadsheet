<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class ChartsOpenpyxlTest extends TestCase
{
    private const DIRECTORY = 'samples' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    public function testBubble2(): void
    {
        $file = self::DIRECTORY . '32readwriteBubbleChart2.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getChartCount());

        self::assertSame('Sheet', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertEmpty($chart->getTitle());
        self::assertTrue($chart->getOneCellAnchor());

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $labels = $dataSeries->getPlotLabels();
        self::assertCount(2, $labels);
        self::assertSame(['2013'], $labels[0]->getDataValues());
        self::assertSame(['2014'], $labels[1]->getDataValues());

        $plotCategories = $dataSeries->getPlotCategories();
        self::assertCount(2, $plotCategories);
        $categories = $plotCategories[0];
        self::assertSame('Number', $categories->getDataType());
        self::assertSame('\'Sheet\'!$A$2:$A$5', $categories->getDataSource());
        self::assertFalse($categories->getBubble3D());
        $categories = $plotCategories[1];
        self::assertCount(2, $plotCategories);
        self::assertSame('Number', $categories->getDataType());
        self::assertSame('\'Sheet\'!$A$7:$A$10', $categories->getDataSource());
        self::assertFalse($categories->getBubble3D());

        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(2, $plotValues);
        $values = $plotValues[0];
        self::assertSame('Number', $values->getDataType());
        self::assertSame('\'Sheet\'!$B$2:$B$5', $values->getDataSource());
        self::assertFalse($values->getBubble3D());
        $values = $plotValues[1];
        self::assertCount(2, $plotValues);
        self::assertSame('Number', $values->getDataType());
        self::assertSame('\'Sheet\'!$B$7:$B$10', $values->getDataSource());
        self::assertFalse($values->getBubble3D());

        $plotValues = $dataSeries->getPlotBubbleSizes();
        self::assertCount(2, $plotValues);
        $values = $plotValues[0];
        self::assertSame('Number', $values->getDataType());
        self::assertSame('\'Sheet\'!$C$2:$C$5', $values->getDataSource());
        self::assertFalse($values->getBubble3D());
        $values = $plotValues[1];
        self::assertCount(2, $plotValues);
        self::assertSame('Number', $values->getDataType());
        self::assertSame('\'Sheet\'!$C$7:$C$10', $values->getDataSource());
        self::assertFalse($values->getBubble3D());

        $spreadsheet->disconnectWorksheets();
    }

    public function testXml(): void
    {
        $infile = self::DIRECTORY . '32readwriteBubbleChart2.xlsx';
        $file = 'zip://';
        $file .= $infile;
        $file .= '#xl/charts/chart1.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected tags
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertSame(0, substr_count($data, 'c:'), 'unusual choice of prefix');
            self::assertSame(0, substr_count($data, 'bubbleScale'));
            self::assertSame(1, substr_count($data, '<tx><v>2013</v></tx>'), 'v tag for 2013');
            self::assertSame(1, substr_count($data, '<tx><v>2014</v></tx>'), 'v tag for 2014');
            self::assertSame(0, substr_count($data, 'numCache'), 'no cached values');
        }
        $file = 'zip://';
        $file .= $infile;
        $file .= '#xl/drawings/_rels/drawing1.xml.rels';
        $data = file_get_contents($file);
        // confirm that file contains expected tags
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertSame(1, substr_count($data, 'Target="/xl/charts/chart1.xml"'), 'Unusual absolute address in drawing rels file');
        }
        $file = 'zip://';
        $file .= $infile;
        $file .= '#xl/worksheets/_rels/sheet1.xml.rels';
        $data = file_get_contents($file);
        // confirm that file contains expected tags
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertSame(1, substr_count($data, 'Target="/xl/drawings/drawing1.xml"'), 'Unusual absolute address in worksheet rels file');
        }
    }
}
