<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

function getTitleText($title)
{
    if (null === $title || null === $title->getCaption()) {
        return null;
    }

    return implode("\n", array_map(function ($rt) {
        return $rt->getPlainText();
    }, $title->getCaption()));
}

class ChartsTitleTest extends TestCase
{
    public function testChartTitles(): void
    {
        $filename = 'tests/data/Reader/XLSX/excelChartsTest.xlsx';
        $reader = IOFactory::createReader('Xlsx')->setIncludeCharts(true);
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $charts = $worksheet->getChartCollection();
        self::assertEquals(5, $worksheet->getChartCount());
        self::assertCount(5, $charts);

        // No title or axis labels
        $chart1 = $charts[0];
        $title = getTitleText($chart1->getTitle());
        self::assertEmpty($title);
        self::assertEmpty(getTitleText($chart1->getXAxisLabel()));
        self::assertEmpty(getTitleText($chart1->getYAxisLabel()));

        // Title, no axis labels
        $chart2 = $charts[1];

        self::assertEquals('Chart with Title and no Axis Labels', getTitleText($chart2->getTitle()));
        self::assertEmpty(getTitleText($chart2->getXAxisLabel()));
        self::assertEmpty(getTitleText($chart2->getYAxisLabel()));

        // No title, only horizontal axis label
        $chart3 = $charts[2];
        self::assertEmpty(getTitleText($chart3->getTitle()));
        self::assertEquals('Horizontal Axis Title Only', getTitleText($chart3->getXAxisLabel()));
        self::assertEmpty(getTitleText($chart3->getYAxisLabel()));

        // No title, only vertical axis label
        $chart4 = $charts[3];
        self::assertEmpty(getTitleText($chart4->getTitle()));
        self::assertEquals('Vertical Axis Title Only', getTitleText($chart4->getYAxisLabel()));
        self::assertEmpty(getTitleText($chart4->getXAxisLabel()));

        // Title and both axis labels
        $chart5 = $charts[4];
        self::assertEquals('Complete Annotations', getTitleText($chart5->getTitle()));
        self::assertEquals('Horizontal Axis Title', getTitleText($chart5->getXAxisLabel()));
        self::assertEquals('Vertical Axis Title', getTitleText($chart5->getYAxisLabel()));
    }
}
