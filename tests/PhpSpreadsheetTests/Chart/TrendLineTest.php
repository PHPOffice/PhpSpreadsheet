<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class TrendLineTest extends AbstractFunctional
{
    private const DIRECTORY = 'samples' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testTrendLine(): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChartTrendlines1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getSheet(1);
        self::assertSame(2, $sheet->getChartCount());
        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheet(1);
        self::assertSame('Scatter Chart', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(2, $charts);

        $chart = $charts[0];
        self::assertNotNull($chart);
        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $plotSeriesArray = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeriesArray);
        $plotSeries = $plotSeriesArray[0];
        $valuesArray = $plotSeries->getPlotValues();
        self::assertCount(3, $valuesArray);
        self::assertEmpty($valuesArray[0]->getTrendLines());
        self::assertEmpty($valuesArray[1]->getTrendLines());
        self::assertEmpty($valuesArray[2]->getTrendLines());

        $chart = $charts[1];
        self::assertNotNull($chart);
        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $plotSeriesArray = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeriesArray);
        $plotSeries = $plotSeriesArray[0];
        $valuesArray = $plotSeries->getPlotValues();
        self::assertCount(1, $valuesArray);
        $trendLines = $valuesArray[0]->getTrendLines();
        self::assertCount(3, $trendLines);

        $trendLine = $trendLines[0];
        self::assertSame('linear', $trendLine->getTrendLineType());
        self::assertFalse($trendLine->getDispRSqr());
        self::assertFalse($trendLine->getDispEq());
        $lineColor = $trendLine->getLineColor();
        self::assertSame('accent4', $lineColor->getValue());
        self::assertSame('stealth', $trendLine->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals(0.5, $trendLine->getLineStyleProperty('width'));
        self::assertSame('', $trendLine->getName());
        self::assertSame(0.0, $trendLine->getBackward());
        self::assertSame(0.0, $trendLine->getForward());
        self::assertSame(0.0, $trendLine->getIntercept());

        $trendLine = $trendLines[1];
        self::assertSame('poly', $trendLine->getTrendLineType());
        self::assertTrue($trendLine->getDispRSqr());
        self::assertTrue($trendLine->getDispEq());
        $lineColor = $trendLine->getLineColor();
        self::assertSame('accent3', $lineColor->getValue());
        self::assertNull($trendLine->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals(1.25, $trendLine->getLineStyleProperty('width'));
        self::assertSame('metric3 polynomial', $trendLine->getName());
        self::assertSame(20.0, $trendLine->getBackward());
        self::assertSame(28.0, $trendLine->getForward());
        self::assertSame(14400.5, $trendLine->getIntercept());

        $trendLine = $trendLines[2];
        self::assertSame('movingAvg', $trendLine->getTrendLineType());
        self::assertTrue($trendLine->getDispRSqr());
        self::assertFalse($trendLine->getDispEq());
        $lineColor = $trendLine->getLineColor();
        self::assertSame('accent2', $lineColor->getValue());
        self::assertNull($trendLine->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertEquals(1.5, $trendLine->getLineStyleProperty('width'));
        self::assertSame('', $trendLine->getName());
        self::assertSame(0.0, $trendLine->getBackward());
        self::assertSame(0.0, $trendLine->getForward());
        self::assertSame(0.0, $trendLine->getIntercept());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
