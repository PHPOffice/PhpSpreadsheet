<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ChartsDynamicTitleTest extends AbstractFunctional
{
    protected function tearDown(): void
    {
        Settings::unsetChartRenderer();
    }

    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testDynamicTitle(): void
    {
        // based on samples/templates/issue.3797.2007.xlsx
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Only Sheet');
        $sheet->fromArray(
            [
                ['Some Title'],
                [],
                [null, null, 'Data'],
                [null, 'L1', 1.3],
                [null, 'L2', 1.3],
                [null, 'L3', 2.3],
                [null, 'L4', 1.6],
                [null, 'L5', 1.5],
                [null, 'L6', 1.4],
                [null, 'L7', 2.2],
                [null, 'L8', 1.8],
                [null, 'L9', 1.1],
                [null, 'L10', 1.8],
                [null, 'L11', 1.6],
                [null, 'L12', 2.7],
                [null, 'L13', 2.2],
                [null, 'L14', 1.3],
            ]
        );

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '\'Only Sheet\'!$B$4', null, 1), // 2010
        ];
        // Set the X-Axis Labels
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, '\'Only Sheet\'!$B$4:$B$17'),
        ];
        // Set the Data values for each data series we want to plot
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, '\'Only Sheet\'!$C$4:$C$17'),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, // plotType
            DataSeries::GROUPING_STANDARD, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues, // plotValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        $title = new Title();
        $title->setCellReference('\'Only Sheet\'!$A$1');
        $font = new Font();
        $font->setCap(Font::CAP_ALL);
        $title->setFont($font);

        // Create the chart
        $chart = new Chart(
            'chart1', // name
            $title, // title
            null, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            null,  // yAxisLabel
            null, // xAxis
        );

        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('G7');
        $chart->setBottomRightPosition('N21');
        // Add the chart to the worksheet
        $sheet->addChart($chart);
        $sheet->setSelectedCells('D1');

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $charts2 = $rsheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        $original = $chart2->getTitle()?->getCalculatedTitle($reloadedSpreadsheet);
        self::assertSame('Some Title', $original);
        $rsheet->getCell('A1')->setValue('Changed Title');
        self::assertNotNull($chart2->getTitle());
        self::assertSame('Changed Title', $chart2->getTitle()->getCalculatedTitle($reloadedSpreadsheet));
        self::assertSame(Font::CAP_ALL, $chart2->getTitle()->getFont()?->getCap());

        $writer = new Html($reloadedSpreadsheet);
        Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\MtJpGraphRenderer::class);
        $writer->setIncludeCharts(true);
        $content = $writer->generateHtmlAll();
        self::assertStringContainsString('alt="Changed Title"', $content);
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
