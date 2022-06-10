<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Charts32XmlTest extends TestCase
{
    // These tests can only be performed by examining xml.
    private const DIRECTORY = 'samples' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    /**
     * @dataProvider providerScatterCharts
     */
    public function testBezierCount(int $expectedCount, string $inputFile): void
    {
        $file = self::DIRECTORY . $inputFile;
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writerChart = new XlsxWriter\Chart($writer);
        $data = $writerChart->writeChart($chart);
        $spreadsheet->disconnectWorksheets();

        self::assertSame(1, substr_count($data, '<c:scatterStyle val='));
        self::assertSame($expectedCount ? 1 : 0, substr_count($data, '<c:scatterStyle val="smoothMarker"/>'));
        self::assertSame($expectedCount, substr_count($data, '<c:smooth val="1"/>'));
    }

    public function providerScatterCharts(): array
    {
        return [
            'no line' => [0, '32readwriteScatterChart1.xlsx'],
            'smooth line (Bezier)' => [3, '32readwriteScatterChart2.xlsx'],
            'straight line' => [0, '32readwriteScatterChart3.xlsx'],
        ];
    }

    public function testAreaPercentageNoCat(): void
    {
        $file = self::DIRECTORY . '32readwriteAreaPercentageChart1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writerChart = new XlsxWriter\Chart($writer);
        $data = $writerChart->writeChart($chart);
        $spreadsheet->disconnectWorksheets();

        // confirm that file contains expected tags
        self::assertSame(0, substr_count($data, '<c:cat>'));
    }

    /**
     * @dataProvider providerCatAxValAx
     */
    public function testCatAxValAx(?bool $numeric): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChart1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        $xAxis = $chart->getChartAxisX();
        self::assertSame(Properties::FORMAT_CODE_GENERAL, $xAxis->getAxisNumberFormat());
        if (is_bool($numeric)) {
            $xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_GENERAL, true);
        }
        $yAxis = $chart->getChartAxisY();
        self::assertSame(Properties::FORMAT_CODE_GENERAL, $yAxis->getAxisNumberFormat());
        if (is_bool($numeric)) {
            $xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_GENERAL, $numeric);
            $yAxis->setAxisNumberProperties(Properties::FORMAT_CODE_GENERAL, $numeric);
        }

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writerChart = new XlsxWriter\Chart($writer);
        $data = $writerChart->writeChart($chart);
        $spreadsheet->disconnectWorksheets();

        if ($numeric === true) {
            self::assertSame(0, substr_count($data, '<c:catAx>'));
            self::assertSame(2, substr_count($data, '<c:valAx>'));
        } else {
            self::assertSame(1, substr_count($data, '<c:catAx>'));
            self::assertSame(1, substr_count($data, '<c:valAx>'));
        }
    }

    public function providerCatAxValAx(): array
    {
        return [
            [true],
            [false],
            [null],
        ];
    }

    public function testAreaPrstClr(): void
    {
        $file = self::DIRECTORY . '32readwriteAreaChart4.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writerChart = new XlsxWriter\Chart($writer);
        $data = $writerChart->writeChart($chart);
        $spreadsheet->disconnectWorksheets();

        self::assertSame(
            1,
            substr_count(
                $data,
                '</c:tx><c:spPr><a:solidFill><a:prstClr val="red"/>'
            )
        );
    }
}
