<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue4201Test extends AbstractFunctional
{
    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testLabelFont(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // Sample data for pie chart
        $data = [
            ['Category', 'Value'],
            ['Category A', 40],
            ['Category B', 30],
            ['Category C', 20],
            ['Category D', 10],
        ];
        $worksheet->fromArray($data, null, 'A1');
        $worksheet->getColumnDimension('A')->setAutoSize(true);

        // Create data series for the pie chart
        $categories = [new DataSeriesValues('String', 'Worksheet!$A$2:$A$5', null, 4)];
        $values = [new DataSeriesValues('Number', 'Worksheet!$B$2:$B$5', null, 4)];

        // Set layout for data labels
        $font = new Font();
        $font->setName('Times New Roman');
        $font->setSize(8);
        $layout = new Layout();
        $layout->setShowVal(true); // Display values
        $layout->setShowCatName(true); // Display category names
        $layout->setLabelFont($font);

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART, // Chart type: Pie chart
            null,
            range(0, count($values) - 1),
            [],
            $categories,
            $values
        );

        $plotArea = new PlotArea($layout, [$series]);
        $chart = new Chart('Pie Chart', null, null, $plotArea);
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');
        $worksheet->addChart($chart);

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart2 = $charts[0];
        self::assertNotNull($chart2);
        $plotArea2 = $chart2->getPlotArea();
        self::assertNotNull($plotArea2);
        $layout2 = $plotArea2->getLayout();
        self::assertNotNull($layout2);
        $font2 = $layout2->getLabelFont();
        self::assertNotNull($font2);
        self::assertSame('Times New Roman', $font2->getLatin());
        self::assertSame(8.0, $font2->getSize());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
