<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PHPUnit\Framework\TestCase;

class SheetsXlsxChartTest extends TestCase
{
    public function testLoadSheetsXlsxChart(): void
    {
        $filename = 'tests/data/Reader/XLSX/sheetsChartsTest.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename, IReader::LOAD_WITH_CHARTS);
        $worksheet = $spreadsheet->getActiveSheet();

        $charts = $worksheet->getChartCollection();
        self::assertEquals(2, $worksheet->getChartCount());
        self::assertCount(2, $charts);

        $chart1 = $charts[0];
        self::assertNotNull($chart1);
        $pa1 = $chart1->getPlotArea();
        self::assertNotNull($pa1);
        self::assertEquals(2, $pa1->getPlotSeriesCount());

        $pg1 = $pa1->getPlotGroup()[0];

        self::assertEquals(DataSeries::TYPE_LINECHART, $pg1->getPlotType());
        self::assertCount(2, $pg1->getPlotLabels());
        self::assertCount(2, $pg1->getPlotValues());
        self::assertCount(2, $pg1->getPlotCategories());

        $chart2 = $charts[1];
        self::assertNotNull($chart2);
        $pa1 = $chart2->getPlotArea();
        self::assertNotNull($pa1);
        self::assertEquals(2, $pa1->getPlotSeriesCount());

        $pg1 = $pa1->getPlotGroupByIndex(0);
        //Before a refresh, data values are empty
        foreach ($pg1->getPlotValues() as $dv) {
            self::assertEmpty($dv->getPointCount());
        }
        $pg1->refresh($worksheet);
        foreach ($pg1->getPlotValues() as $dv) {
            self::assertEquals(9, $dv->getPointCount());
        }
        self::assertEquals(DataSeries::TYPE_SCATTERCHART, $pg1->getPlotType());
        self::assertCount(2, $pg1->getPlotLabels());
        self::assertCount(2, $pg1->getPlotValues());
        self::assertCount(2, $pg1->getPlotCategories());
    }
}
