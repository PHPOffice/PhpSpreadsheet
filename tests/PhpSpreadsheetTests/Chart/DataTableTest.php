<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\DataTable;
//use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Theme as SpreadsheetTheme;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DataTableTest extends AbstractFunctional
{
    public function testSetDataTable(): void
    {
        $dataTable = new DataTable();
        self::assertTrue($dataTable->getShowHorizontalBorder());
        self::assertTrue($dataTable->getShowVerticalBorder());
        self::assertTrue($dataTable->getShowOutline());
        self::assertTrue($dataTable->getShowKeys());
        $dataTable->setShowHorizontalBorder(false)
            ->setShowVerticalBorder(false)
            ->setShowOutline(false)
            ->setShowKeys(false);
        self::assertFalse($dataTable->getShowHorizontalBorder());
        self::assertFalse($dataTable->getShowVerticalBorder());
        self::assertFalse($dataTable->getShowOutline());
        self::assertFalse($dataTable->getShowKeys());
    }

    private function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    private function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testCopyDataTable(): void
    {
        $spreadsheet = new Spreadsheet();
        // based on 33_Chart_create_area3.
        $spreadsheet->getTheme()
            ->setThemeColorName(
                SpreadsheetTheme::COLOR_SCHEME_2013_2022_NAME
            );
        $worksheet = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $worksheet->fromArray(
            [
                ['', 2010, 2011, 2012],
                ['Q1', 12, 15, 21],
                ['Q2', 56, 73, 86],
                ['Q3', 52, 61, 69],
                ['Q4', 30, 32, 0],
            ]
        );

        // Set the Labels for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), // 2010
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), // 2011
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), // 2012
        ];
        // Set the X-Axis Labels
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
        ];
        // Set the Data values for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$5', null, 4),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_AREACHART, // plotType
            DataSeries::GROUPING_PERCENT_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues          // plotValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        $plotArea->setDataTable(new DataTable());
        // No need for Legend if using DataTable
        $legend = null; //new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

        $title = new Title('Test %age-Stacked Area Chart');
        $yAxisLabel = new Title('Value ($k)');

        // Create the chart
        $chart = new Chart(
            'chart1', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('A1');
        $chart->setBottomRightPosition('H18');

        // Add the chart to the worksheet
        $sheet2->addChart($chart);
        $spreadsheet->setActiveSheetIndex(1);

        // Save Excel 2007 file
        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload(
            $spreadsheet,
            'Xlsx',
            $this->readCharts(...),
            $this->writeCharts(...)
        );
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $charts2 = $sheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        $plotArea = $chart2->getPlotArea();
        self::assertNotNull($plotArea);
        $dtab = $plotArea->getDataTable();
        self::assertNotNull($dtab);
        self::assertTrue($dtab->getShowHorizontalBorder());
        self::assertTrue($dtab->getShowVerticalBorder());
        self::assertTrue($dtab->getShowOutline());
        self::assertTrue($dtab->getShowKeys());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
