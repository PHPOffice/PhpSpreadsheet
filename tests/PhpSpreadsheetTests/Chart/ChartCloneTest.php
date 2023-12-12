<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

/**
 * Confirm cloning worksheet works as expected.
 * This class tests everything except:
 *   DataSeriesValues - no coverage for fillColor as array.
 *   Title - no coverage for caption when not array.
 */
class ChartCloneTest extends AbstractFunctional
{
    private const DIRECTORY = 'samples' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    public function testCloneSheet(): void
    {
        $file = self::DIRECTORY . '32readwriteLineChart5.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $oldSheet = $spreadsheet->getActiveSheet();

        $sheet = clone $oldSheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $oldCharts = $oldSheet->getChartCollection();
        self::assertCount(1, $oldCharts);
        $oldChart = $oldCharts[0];
        self::assertNotNull($oldChart);

        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertNotSame($oldChart, $chart);

        self::assertSame('ffffff', $chart->getFillColor()->getValue());
        self::assertSame('srgbClr', $chart->getFillColor()->getType());
        self::assertSame('d9d9d9', $chart->getBorderLines()->getLineColorProperty('value'));
        self::assertSame('srgbClr', $chart->getBorderLines()->getLineColorProperty('type'));
        self::assertEqualsWithDelta(9360 / Properties::POINTS_WIDTH_MULTIPLIER, $chart->getBorderLines()->getLineStyleProperty('width'), 1.0E-8);
        self::assertTrue($chart->getChartAxisY()->getNoFill());
        self::assertFalse($chart->getChartAxisX()->getNoFill());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCloneSheetWithLegendAndTitle(): void
    {
        $file = self::DIRECTORY . '32readwriteChartWithImages1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $oldSheet = $spreadsheet->getActiveSheet();

        $sheet = clone $oldSheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $oldCharts = $oldSheet->getChartCollection();
        self::assertCount(1, $oldCharts);
        $oldChart = $oldCharts[0];
        self::assertNotNull($oldChart);

        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertNotSame($oldChart, $chart);

        self::assertNotNull($chart->getLegend());
        self::assertNotSame($chart->getLegend(), $oldChart->getLegend());
        self::assertNotNull($chart->getTitle());
        self::assertNotSame($chart->getTitle(), $oldChart->getTitle());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCloneSheetWithBubbleSizes(): void
    {
        $file = self::DIRECTORY . '32readwriteBubbleChart2.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $oldSheet = $spreadsheet->getActiveSheet();

        $sheet = clone $oldSheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $oldCharts = $oldSheet->getChartCollection();
        self::assertCount(1, $oldCharts);
        $oldChart = $oldCharts[0];
        self::assertNotNull($oldChart);

        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertNotSame($oldChart, $chart);

        $oldGroup = $oldChart->getPlotArea()?->getPlotGroup();
        self::assertNotNull($oldGroup);
        self::assertCount(1, $oldGroup);
        $oldSizes = $oldGroup[0]->getPlotBubbleSizes();
        self::assertCount(2, $oldSizes);

        $plotGroup = $chart->getPlotArea()?->getPlotGroup();
        self::assertNotNull($plotGroup);
        self::assertCount(1, $plotGroup);
        $bubbleSizes = $plotGroup[0]->getPlotBubbleSizes();
        self::assertCount(2, $bubbleSizes);
        self::assertNotSame($bubbleSizes, $oldSizes);

        $spreadsheet->disconnectWorksheets();
    }

    public function testCloneSheetWithTrendLines(): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChartTrendlines1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $oldSheet = $spreadsheet->getActiveSheet();

        $sheet = clone $oldSheet;
        $sheet->setTitle('test2');
        $spreadsheet->addsheet($sheet);

        $oldCharts = $oldSheet->getChartCollection();
        self::assertCount(2, $oldCharts);
        $oldChart = $oldCharts[1];
        self::assertNotNull($oldChart);

        $charts = $sheet->getChartCollection();
        self::assertCount(2, $charts);
        $chart = $charts[1];
        self::assertNotNull($chart);
        self::assertNotSame($oldChart, $chart);

        $oldGroup = $chart->getPlotArea()?->getPlotGroup();
        self::assertNotNull($oldGroup);
        self::assertCount(1, $oldGroup);
        $oldLabels = $oldGroup[0]->getPlotLabels();
        self::assertCount(1, $oldLabels);
        self::assertCount(3, $oldLabels[0]->getTrendLines());

        $plotGroup = $chart->getPlotArea()?->getPlotGroup();
        self::assertNotNull($plotGroup);
        self::assertCount(1, $plotGroup);
        $plotLabels = $plotGroup[0]->getPlotLabels();
        self::assertCount(1, $plotLabels);
        self::assertCount(3, $plotLabels[0]->getTrendLines());

        $spreadsheet->disconnectWorksheets();
    }
}
