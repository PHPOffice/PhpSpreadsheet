<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ChartBorderTest extends AbstractFunctional
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

    public function testChartBorder(): void
    {
        $file = self::DIRECTORY . '32readwriteLineChart5.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(1, $sheet->getChartCount());
        /** @var callable */
        $callableReader = [$this, 'readCharts'];
        /** @var callable */
        $callableWriter = [$this, 'writeCharts'];
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx', $callableReader, $callableWriter);
        $spreadsheet->disconnectWorksheets();

        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertSame('ffffff', $chart->getFillColor()->getValue());
        self::assertSame('srgbClr', $chart->getFillColor()->getType());
        self::assertSame('d9d9d9', $chart->getBorderLines()->getLineColorProperty('value'));
        self::assertSame('srgbClr', $chart->getBorderLines()->getLineColorProperty('type'));
        self::assertEqualsWithDelta(9360 / Properties::POINTS_WIDTH_MULTIPLIER, $chart->getBorderLines()->getLineStyleProperty('width'), 1.0E-8);
        self::assertTrue($chart->getChartAxisY()->getNoFill());
        self::assertFalse($chart->getChartAxisX()->getNoFill());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
