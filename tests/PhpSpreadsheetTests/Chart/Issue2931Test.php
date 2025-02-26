<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Issue2931Test extends TestCase
{
    public function testSurface(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['5-6']),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['6-7']),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, null, null, 1, ['7-8']),
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, null, null, 9, [1, 2, 3, 4, 5, 6, 7, 8, 9]),
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, null, null, 9, [6, 6, 6, 6, 6, 6, 5.9, 6, 6]),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, null, null, 9, [6, 6, 6, 6.5, 7, 7, 7, 7, 7]),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, null, null, 9, [6, 6, 6, 7, 8, 8, 8, 8, 7.9]),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_SURFACECHART,
            DataSeries::GROUPING_STANDARD, // grouping should not be written for surface chart
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues,
            null, // plotDirection
            false, // smooth line
            DataSeries::STYLE_LINEMARKER  // plotStyle
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new ChartLegend(ChartLegend::POSITION_BOTTOM, null, false);

        $title = new Title('График распредления температур в пределах кр');

        $chart = new Chart(
            'chart2',
            $title,
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
        );

        $chart->setTopLeftPosition('$A$1');
        $chart->setBottomRightPosition('$P$20');

        $sheet->addChart($chart);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writerChart = new XlsxWriter\Chart($writer);
        $data = $writerChart->writeChart($chart);

        // rotX etc. should be generated for surfaceChart 2D
        //    even when unspecified.
        $expectedXml2D = [
            '<c:view3D><c:rotX val="90"/><c:rotY val="0"/><c:rAngAx val="0"/><c:perspective val="0"/></c:view3D>',
        ];
        $expectedXml3D = [
            '<c:view3D/>',
        ];
        $expectedXmlNoX = [
            'c:grouping',
        ];

        // confirm that file contains expected tags
        foreach ($expectedXml2D as $expected) {
            self::assertSame(1, substr_count($data, $expected), $expected);
        }
        foreach ($expectedXmlNoX as $expected) {
            self::assertSame(0, substr_count($data, $expected), $expected);
        }

        $series->setPlotType(DataSeries::TYPE_SURFACECHART_3D);
        $plotArea = new PlotArea(null, [$series]);
        $chart->setPlotArea($plotArea);
        $writerChart = new XlsxWriter\Chart($writer);
        $data = $writerChart->writeChart($chart);
        // confirm that file contains expected tags
        foreach ($expectedXml3D as $expected) {
            self::assertSame(1, substr_count($data, $expected), $expected);
        }
        foreach ($expectedXmlNoX as $expected) {
            self::assertSame(0, substr_count($data, $expected), $expected);
        }

        $spreadsheet->disconnectWorksheets();
    }
}
