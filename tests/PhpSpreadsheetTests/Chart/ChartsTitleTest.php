<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class ChartsTitleTest extends TestCase
{
    private static function getTitleText(?Title $title): string
    {
        return ($title === null) ? '' : $title->getCaptionText();
    }

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
        self::assertNotNull($chart1);
        $title = self::getTitleText($chart1->getTitle());
        self::assertEmpty($title);
        self::assertEmpty(self::getTitleText($chart1->getXAxisLabel()));
        self::assertEmpty(self::getTitleText($chart1->getYAxisLabel()));

        // Title, no axis labels
        $chart2 = $charts[1];
        self::assertNotNull($chart2);

        self::assertEquals('Chart with Title and no Axis Labels', self::getTitleText($chart2->getTitle()));
        self::assertEmpty(self::getTitleText($chart2->getXAxisLabel()));
        self::assertEmpty(self::getTitleText($chart2->getYAxisLabel()));

        // No title, only horizontal axis label
        $chart3 = $charts[2];
        self::assertNotNull($chart3);
        self::assertEmpty(self::getTitleText($chart3->getTitle()));
        self::assertEquals('Horizontal Axis Title Only', self::getTitleText($chart3->getXAxisLabel()));
        self::assertEmpty(self::getTitleText($chart3->getYAxisLabel()));

        // No title, only vertical axis label
        $chart4 = $charts[3];
        self::assertNotNull($chart4);
        self::assertEmpty(self::getTitleText($chart4->getTitle()));
        self::assertEquals('Vertical Axis Title Only', self::getTitleText($chart4->getYAxisLabel()));
        self::assertEmpty(self::getTitleText($chart4->getXAxisLabel()));

        // Title and both axis labels
        $chart5 = $charts[4];
        self::assertNotNull($chart5);
        self::assertEquals('Complete Annotations', self::getTitleText($chart5->getTitle()));
        self::assertEquals('Horizontal Axis Title', self::getTitleText($chart5->getXAxisLabel()));
        self::assertEquals('Vertical Axis Title', self::getTitleText($chart5->getYAxisLabel()));
    }
}
