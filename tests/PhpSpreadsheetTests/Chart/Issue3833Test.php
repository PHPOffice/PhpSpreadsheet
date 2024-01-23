<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3833Test extends AbstractFunctional
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

    public function testDisplayUnits1(): void
    {
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load('tests/data/Reader/XLSX/issue.3833.units.xlsx');

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheetByNameOrThrow('charts');
        $charts2 = $sheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        $yAxis = $chart2->getChartAxisY();
        $dispUnits = $yAxis->getAxisOptionsProperty('dispUnitsBuiltIn');
        self::assertSame('tenThousands', $dispUnits);
        $logBase = $yAxis->getAxisOptionsProperty('logBase');
        self::assertNull($logBase);

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testDisplayUnits2(): void
    {
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load('tests/data/Reader/XLSX/issue.3833.units.xlsx');
        $sheet1 = $spreadsheet->getSheetByNameOrThrow('charts');
        $charts1 = $sheet1->getChartCollection();
        self::assertCount(1, $charts1);
        $chart1 = $charts1[0];
        self::assertNotNull($chart1);
        $yAxis1 = $chart1->getChartAxisY();
        $yAxis1->setAxisOption('dispUnitsBuiltIn', 1000);

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheetByNameOrThrow('charts');
        $charts2 = $sheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        $yAxis = $chart2->getChartAxisY();
        $dispUnits = $yAxis->getAxisOptionsProperty('dispUnitsBuiltIn');
        self::assertSame('thousands', $dispUnits);
        $logBase = $yAxis->getAxisOptionsProperty('logBase');
        self::assertNull($logBase);

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testDisplayUnits3(): void
    {
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load('tests/data/Reader/XLSX/issue.3833.units.xlsx');
        $sheet1 = $spreadsheet->getSheetByNameOrThrow('charts');
        $charts1 = $sheet1->getChartCollection();
        self::assertCount(1, $charts1);
        $chart1 = $charts1[0];
        self::assertNotNull($chart1);
        $yAxis1 = $chart1->getChartAxisY();
        $yAxis1->setDispUnitsTitle(null);

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheetByNameOrThrow('charts');
        $charts2 = $sheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        $yAxis = $chart2->getChartAxisY();
        self::assertNull($yAxis->getDispUnitsTitle());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testLogBase(): void
    {
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load('tests/data/Reader/XLSX/issue.3833.logarithm.xlsx');

        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getSheetByNameOrThrow('charts');
        $charts2 = $sheet->getChartCollection();
        self::assertCount(1, $charts2);
        $chart2 = $charts2[0];
        self::assertNotNull($chart2);
        $yAxis = $chart2->getChartAxisY();
        $logBase = $yAxis->getAxisOptionsProperty('logBase');
        self::assertSame('10', $logBase);
        $dispUnits = $yAxis->getAxisOptionsProperty('dispUnitsBuiltIn');
        self::assertNull($dispUnits);
        $yAxis->setAxisOption('dispUnitsBuiltIn', 1000000000000);
        $dispUnits = $yAxis->getAxisOptionsProperty('dispUnitsBuiltIn');
        // same logic as in Writer/Xlsx/Chart for 32-bit safety
        $dispUnits = ($dispUnits == Axis::TRILLION_INDEX) ? Axis::DISP_UNITS_TRILLIONS : (is_numeric($dispUnits) ? (Axis::DISP_UNITS_BUILTIN_INT[(int) $dispUnits] ?? '') : $dispUnits);
        self::assertSame('trillions', $dispUnits);

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
