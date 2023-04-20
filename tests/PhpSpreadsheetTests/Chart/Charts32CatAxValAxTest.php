<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Charts32CatAxValAxTest extends TestCase
{
    // These tests can only be performed by examining xml.
    // They are based on sample 33_Chart_Create_Scatter2.

    /** @var string */
    private $outputFileName = '';

    private const FORMAT_CODE_DATE_ISO8601_SLASH = 'yyyy/mm/dd'; // not automatically treated as numeric

    protected function tearDown(): void
    {
        if ($this->outputFileName !== '') {
            unlink($this->outputFileName);
            $this->outputFileName = '';
        }
    }

    /**
     * @dataProvider providerCatAxValAx
     */
    public function test1CatAx1ValAx(?bool $numeric): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        // changed data to simulate a trend chart - Xaxis are dates; Yaxis are 3 meausurements from each date
        $worksheet->fromArray(
            [
                ['', 'metric1', 'metric2', 'metric3'],
                ['=DATEVALUE("2021-01-01")', 12.1, 15.1, 21.1],
                ['=DATEVALUE("2021-01-04")', 56.2, 73.2, 86.2],
                ['=DATEVALUE("2021-01-07")', 52.2, 61.2, 69.2],
                ['=DATEVALUE("2021-01-10")', 30.2, 32.2, 0.2],
            ]
        );
        $worksheet->getStyle('A2:A5')->getNumberFormat()->setFormatCode(self::FORMAT_CODE_DATE_ISO8601_SLASH);
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
        // Added so that Xaxis shows dates instead of Excel-equivalent-year1900-numbers
        $xAxis = new Axis();
        //$xAxis->setAxisNumberProperties(Properties::FORMAT_CODE_DATE );
        if (is_bool($numeric)) {
            $xAxis->setAxisNumberProperties(self::FORMAT_CODE_DATE_ISO8601_SLASH, $numeric);
        } else {
            $xAxis->setAxisNumberProperties(self::FORMAT_CODE_DATE_ISO8601_SLASH);
        }

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
            //DataSeries::STYLE_LINEMARKER  // plotStyle
            DataSeries::STYLE_MARKER  // plotStyle
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
            $yAxisLabel,  // yAxisLabel
            // added xAxis for correct date display
            $xAxis, // xAxis
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('P20');
        // Add the chart to the worksheet
        $worksheet->addChart($chart);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $this->outputFileName = File::temporaryFilename();
        $writer->save($this->outputFileName);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFileName;
        $file .= '#xl/charts/chart1.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected tags
        if ($data === false) {
            self::fail('Unable to read file');
        } elseif ($numeric === true) {
            self::assertSame(0, substr_count($data, '<c:catAx'));
            self::assertSame(2, substr_count($data, '<c:valAx'));
        } else {
            self::assertSame(1, substr_count($data, '<c:catAx'));
            self::assertSame(1, substr_count($data, '<c:valAx'));
        }
    }

    public static function providerCatAxValAx(): array
    {
        return [
            [true],
            [false],
            [null],
        ];
    }
}
