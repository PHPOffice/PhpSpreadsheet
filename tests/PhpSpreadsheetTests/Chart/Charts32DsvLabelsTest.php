<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Charts32DsvLabelsTest extends AbstractFunctional
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

    public function testBar4(): void
    {
        $file = self::DIRECTORY . '32readwriteBarChart4.xlsx';
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

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $dataSeriesArray = $plotArea->getPlotGroup();
        self::assertCount(1, $dataSeriesArray);
        $dataSeries = $dataSeriesArray[0];
        $dataSeriesValuesArray = $dataSeries->getPlotValues();
        self::assertCount(1, $dataSeriesValuesArray);
        $dataSeriesValues = $dataSeriesValuesArray[0];
        $layout = $dataSeriesValues->getLabelLayout();
        self::assertNotNull($layout);
        self::assertTrue($layout->getShowVal());
        $fillColor = $layout->getLabelFillColor();
        self::assertNotNull($fillColor);
        self::assertSame('schemeClr', $fillColor->getType());
        self::assertSame('accent1', $fillColor->getValue());
        $borderColor = $layout->getLabelBorderColor();
        self::assertNotNull($borderColor);
        self::assertSame('srgbClr', $borderColor->getType());
        self::assertSame('FFC000', $borderColor->getValue());
        $fontColor = $layout->getLabelFontColor();
        self::assertNotNull($fontColor);
        self::assertSame('srgbClr', $fontColor->getType());
        self::assertSame('FFFF00', $fontColor->getValue());
        self::assertEquals(
            [15, 73, 61, 32],
            $dataSeriesValues->getDataValues()
        );

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
