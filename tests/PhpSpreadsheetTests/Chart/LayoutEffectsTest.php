<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class LayoutEffectsTest extends AbstractFunctional
{
    private const FILENAME = 'samples/templates/32readwriteLineChart6.xlsx';

    public function readCharts(XlsxReader $reader): void
    {
        $reader->setIncludeCharts(true);
    }

    public function writeCharts(XlsxWriter $writer): void
    {
        $writer->setIncludeCharts(true);
    }

    public function testLegend(): void
    {
        $reader = new XlsxReader();
        $this->readCharts($reader);
        $spreadsheet = $reader->load(self::FILENAME);

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
        $yAxis = $chart2->getChartAxisY();
        $yAxisText = $yAxis->getAxisText();
        self::assertNotNull($yAxisText);
        self::assertSame(['value' => 'accent4', 'type' => 'schemeClr', 'alpha' => 60], $yAxisText->getGlowProperty('color'));
        $plotArea2 = $chart2->getPlotArea();
        self::assertNotNull($plotArea2);
        $plotGroup2 = $plotArea2->getPlotGroup()[0];
        $plotIndex2 = $plotGroup2->getPlotLabelByIndex(0);
        if ($plotIndex2 === false) {
            self::fail('Unexpected false for getPlotLabelByIndex');
        } else {
            $layout2 = $plotIndex2->getLabelLayout();
            self::assertNotNull($layout2);
            $effects2 = $layout2->getLabelEffects();
            self::assertNotNull($effects2);
            $shadows2 = $effects2->getShadowArray();
            self::assertSame('outerShdw', $shadows2['effect']);
            self::assertSame(['value' => 'FF0000', 'type' => 'srgbClr', 'alpha' => 70], $shadows2['color']);
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
