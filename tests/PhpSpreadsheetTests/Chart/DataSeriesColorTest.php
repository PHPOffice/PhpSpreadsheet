<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis as ChartAxis;
use PhpOffice\PhpSpreadsheet\Chart\AxisText;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DataSeriesColorTest extends AbstractFunctional
{
    // based on 33_Char_create_scatter2
    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testDataSeriesValues(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        // changed data to simulate a trend chart - Xaxis are dates; Yaxis are 3 measurements from each date
        $worksheet->fromArray(
            [
                ['', 'metric1', 'metric2', 'metric3'],
                ['=DATEVALUE("2021-01-01")', 12.1, 15.1, 21.1],
                ['=DATEVALUE("2021-01-04")', 56.2, 73.2, 86.2],
                ['=DATEVALUE("2021-01-07")', 52.2, 61.2, 69.2],
                ['=DATEVALUE("2021-01-10")', 30.2, 32.2, 0.2],
            ]
        );
        $worksheet->getStyle('A2:A5')->getNumberFormat()->setFormatCode(Properties::FORMAT_CODE_DATE_ISO8601);
        $worksheet->getColumnDimension('A')->setAutoSize(true);
        $worksheet->setSelectedCells('A1');

        // Set the Labels for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), // was 2010
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), // was 2011
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), // was 2012
        ];
        // Set the X-Axis Labels
        // changed from STRING to NUMBER
        // added 2 additional x-axis values associated with each of the 3 metrics
        // added FORMATE_CODE_NUMBER
        $xAxisTickValues = [
            //new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$A$2:$A$5', Properties::FORMAT_CODE_DATE, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$A$2:$A$5', Properties::FORMAT_CODE_DATE, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$A$2:$A$5', Properties::FORMAT_CODE_DATE, 4),
        ];
        // Set the Data values for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        // added FORMAT_CODE_NUMBER
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', Properties::FORMAT_CODE_NUMBER, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', Properties::FORMAT_CODE_NUMBER, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$5', Properties::FORMAT_CODE_NUMBER, 4),
        ];

        // series 1
        // marker details
        $dataSeriesValues[0]
            ->setPointMarker('diamond')
            ->setPointSize(5)
            ->getMarkerFillColor()
            ->setColorProperties('0070C0', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
        $dataSeriesValues[0]
            ->getMarkerBorderColor()
            ->setColorProperties('002060', null, ChartColor::EXCEL_COLOR_TYPE_RGB);

        // line details - smooth line, connected
        $dataSeriesValues[0]
            ->setScatterLines(true)
            ->setSmoothLine(true)
            ->setLineColorProperties('accent1', 40, ChartColor::EXCEL_COLOR_TYPE_SCHEME); // value, alpha, type
        $dataSeriesValues[0]->setLineStyleProperties(
            2.5, // width in points
            Properties::LINE_STYLE_COMPOUND_TRIPLE, // compound
            Properties::LINE_STYLE_DASH_SQUARE_DOT, // dash
            Properties::LINE_STYLE_CAP_SQUARE, // cap
            Properties::LINE_STYLE_JOIN_MITER, // join
            Properties::LINE_STYLE_ARROW_TYPE_OPEN, // head type
            (string) Properties::LINE_STYLE_ARROW_SIZE_4, // head size preset index
            Properties::LINE_STYLE_ARROW_TYPE_ARROW, // end type
            (string) Properties::LINE_STYLE_ARROW_SIZE_6 // end size preset index
        );

        // series 2 - straight line - no special effects, connected, straight line
        $dataSeriesValues[1] // square fill
            ->setPointMarker('square')
            ->setPointSize(6)
            ->getMarkerBorderColor()
            ->setColorProperties('accent6', 3, ChartColor::EXCEL_COLOR_TYPE_SCHEME);
        $dataSeriesValues[1] // square border
            ->getMarkerFillColor()
            ->setColorProperties('0FFF00', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
        $dataSeriesValues[1]
            ->setScatterLines(true)
            ->setSmoothLine(false)
            ->setLineColorProperties('FF0000', 80, ChartColor::EXCEL_COLOR_TYPE_RGB);
        $dataSeriesValues[1]->setLineWidth(2.0);

        // series 3 - markers, no line
        $dataSeriesValues[2] // triangle fill
            //->setPointMarker('triangle') // let Excel choose shape
            ->setPointSize(7)
            ->getMarkerFillColor()
            ->setColorProperties('FFFF00', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
        $dataSeriesValues[2] // triangle border
            ->getMarkerBorderColor()
            ->setColorProperties('accent4', null, ChartColor::EXCEL_COLOR_TYPE_SCHEME);
        $dataSeriesValues[2]->setScatterLines(false); // points not connected

        // Added so that Xaxis shows dates instead of Excel-equivalent-year1900-numbers
        $xAxis = new ChartAxis();
        //$xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_DATE );
        $xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_DATE_ISO8601, true);
        //$xAxis->setAxisOption('textRotation', '45');
        $xAxisText = new AxisText();
        $xAxisText->setRotation(45)->getFillColorObject()->setValue('00FF00')->setType(ChartColor::EXCEL_COLOR_TYPE_RGB);
        $xAxis->setAxisText($xAxisText);

        $yAxis = new ChartAxis();
        $yAxis->setLineStyleProperties(
            2.5, // width in points
            Properties::LINE_STYLE_COMPOUND_SIMPLE,
            Properties::LINE_STYLE_DASH_DASH_DOT,
            Properties::LINE_STYLE_CAP_FLAT,
            Properties::LINE_STYLE_JOIN_BEVEL
        );
        $yAxis->setLineColorProperties('ffc000', null, ChartColor::EXCEL_COLOR_TYPE_RGB);
        $yAxisText = new AxisText();
        $yAxisText->setGlowProperties(20.0, 'accent1', 20, ChartColor::EXCEL_COLOR_TYPE_SCHEME);
        $yAxis->setAxisText($yAxisText);

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_SCATTERCHART, // plotType
            null, // plotGrouping (Scatter charts don't have any grouping)
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues, // plotValues
            null, // plotDirection
            false, // smooth line
            DataSeries::STYLE_SMOOTHMARKER  // plotStyle
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

        $title = new Title('Test Scatter Trend Chart');
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
            $yAxisLabel, // yAxisLabel
            // added xAxis for correct date display
            $xAxis, // xAxis
            $yAxis, // yAxis
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('P20');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $charts2 = $sheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        $xAxisText = $chart2->getChartAxisX()->getAxisText();
        $yAxisText = $chart2->getChartAxisY()->getAxisText();
        if ($xAxisText === null || $yAxisText === null) {
            self::fail('Unexpected null x-axis or y-axis');
        } else {
            self::assertSame(45, $xAxisText->getRotation());
            self::assertSame('00FF00', $xAxisText->getFillColorObject()->getValue());
            self::assertSame(ChartColor::EXCEL_COLOR_TYPE_RGB, $xAxisText->getFillColorObject()->getType());
            self::assertSame(20.0, $yAxisText->getGlowProperty('size'));
            self::assertSame(['value' => 'accent1', 'type' => 'schemeClr', 'alpha' => 20], $yAxisText->getGlowProperty('color'));
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
