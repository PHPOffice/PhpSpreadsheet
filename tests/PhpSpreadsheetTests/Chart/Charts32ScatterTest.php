<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Charts32ScatterTest extends AbstractFunctional
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

    public function testScatter1(): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChart1.xlsx';
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
        self::assertSame('Charts', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        $title = $chart->getTitle();
        self::assertNotNull($title);
        $captionArray = $title->getCaption();
        self::assertIsArray($captionArray);
        self::assertCount(1, $captionArray);
        $caption = $captionArray[0];
        self::assertInstanceOf(RichText::class, $caption);
        self::assertSame('Scatter - No Join and Markers', $caption->getPlainText());
        $elements = $caption->getRichTextElements();
        self::assertCount(1, $elements);
        $run = $elements[0];
        self::assertInstanceOf(Run::class, $run);
        $font = $run->getFont();
        self::assertInstanceOf(Font::class, $font);
        self::assertSame('Calibri', $font->getLatin());
        self::assertEquals(12, $font->getSize());
        self::assertTrue($font->getBold());
        self::assertFalse($font->getItalic());
        self::assertFalse($font->getSuperscript());
        self::assertFalse($font->getSubscript());
        self::assertFalse($font->getStrikethrough());
        self::assertSame('none', $font->getUnderline());
        $chartColor = $font->getChartColor();
        self::assertNotNull($chartColor);
        self::assertSame('000000', $chartColor->getValue());
        self::assertSame('srgbClr', $chartColor->getType());

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[1];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[2];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(7, $values->getPointSize());
        // Had been testing for Fill Color, but we actually
        //  meant to test for marker color, which is now distinct.
        self::assertSame('FFFF00', $values->getMarkerFillColor()->getValue());
        self::assertSame('srgbClr', $values->getMarkerFillColor()->getType());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testScatter6(): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChart6.xlsx';
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
        self::assertSame('Charts', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        $title = $chart->getTitle();
        self::assertNotNull($title);
        $captionArray = $title->getCaption();
        self::assertIsArray($captionArray);
        self::assertCount(1, $captionArray);
        $caption = $captionArray[0];
        self::assertInstanceOf(RichText::class, $caption);
        self::assertSame('Scatter - Rich Text Title No Join and Markers', $caption->getPlainText());
        $elements = $caption->getRichTextElements();
        self::assertCount(3, $elements);

        $run = $elements[0];
        self::assertInstanceOf(Run::class, $run);
        $font = $run->getFont();
        self::assertInstanceOf(Font::class, $font);
        self::assertSame('Calibri', $font->getLatin());
        self::assertEquals(12, $font->getSize());
        self::assertTrue($font->getBold());
        self::assertFalse($font->getItalic());
        self::assertFalse($font->getSuperscript());
        self::assertFalse($font->getSubscript());
        self::assertFalse($font->getStrikethrough());
        self::assertSame('none', $font->getUnderline());
        $chartColor = $font->getChartColor();
        self::assertNotNull($chartColor);
        self::assertSame('000000', $chartColor->getValue());
        self::assertSame('srgbClr', $chartColor->getType());

        $run = $elements[1];
        self::assertInstanceOf(Run::class, $run);
        $font = $run->getFont();
        self::assertInstanceOf(Font::class, $font);
        self::assertSame('Courier New', $font->getLatin());
        self::assertEquals(10, $font->getSize());
        self::assertFalse($font->getBold());
        self::assertFalse($font->getItalic());
        self::assertFalse($font->getSuperscript());
        self::assertFalse($font->getSubscript());
        self::assertFalse($font->getStrikethrough());
        self::assertSame('single', $font->getUnderline());
        $chartColor = $font->getChartColor();
        self::assertNotNull($chartColor);
        self::assertSame('00B0F0', $chartColor->getValue());
        self::assertSame('srgbClr', $chartColor->getType());

        $run = $elements[2];
        self::assertInstanceOf(Run::class, $run);
        $font = $run->getFont();
        self::assertInstanceOf(Font::class, $font);
        self::assertSame('Calibri', $font->getLatin());
        self::assertEquals(12, $font->getSize());
        self::assertTrue($font->getBold());
        self::assertFalse($font->getItalic());
        self::assertFalse($font->getSuperscript());
        self::assertFalse($font->getSubscript());
        self::assertFalse($font->getStrikethrough());
        self::assertSame('none', $font->getUnderline());
        $chartColor = $font->getChartColor();
        self::assertNotNull($chartColor);
        self::assertSame('000000', $chartColor->getValue());
        self::assertSame('srgbClr', $chartColor->getType());

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[1];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[2];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(7, $values->getPointSize());
        // Had been testing for Fill Color, but we actually
        //  meant to test for marker color, which is now distinct.
        self::assertSame('FFFF00', $values->getMarkerFillColor()->getValue());
        self::assertSame('srgbClr', $values->getMarkerFillColor()->getType());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testScatter3(): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChart3.xlsx';
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
        self::assertSame('Charts', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        $title = $chart->getTitle();
        self::assertNotNull($title);
        $captionArray = $title->getCaption();
        self::assertIsArray($captionArray);
        self::assertCount(1, $captionArray);
        $caption = $captionArray[0];
        self::assertInstanceOf(RichText::class, $caption);
        self::assertSame('Scatter - Join Straight Lines and Markers', $caption->getPlainText());
        $elements = $caption->getRichTextElements();
        self::assertCount(1, $elements);
        $run = $elements[0];
        self::assertInstanceOf(Run::class, $run);
        $font = $run->getFont();
        self::assertInstanceOf(Font::class, $font);
        self::assertSame('Calibri', $font->getLatin());
        self::assertEquals(12, $font->getSize());
        self::assertTrue($font->getBold());
        self::assertFalse($font->getItalic());
        self::assertFalse($font->getSuperscript());
        self::assertFalse($font->getSubscript());
        self::assertFalse($font->getStrikethrough());
        self::assertSame('none', $font->getUnderline());
        $chartColor = $font->getChartColor();
        self::assertNotNull($chartColor);
        self::assertSame('000000', $chartColor->getValue());
        self::assertSame('srgbClr', $chartColor->getType());

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertTrue($values->getScatterLines());
        // the default value of 1 point is no longer written out
        //   when not explicitly specified.
        self::assertNull($values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[1];
        self::assertTrue($values->getScatterLines());
        self::assertNull($values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[2];
        self::assertTrue($values->getScatterLines());
        self::assertNull($values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testScatter7(): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChart7.xlsx';
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
        self::assertSame('Charts', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        $title = $chart->getTitle();
        self::assertNotNull($title);
        $captionArray = $title->getCaption();
        self::assertIsArray($captionArray);
        self::assertCount(1, $captionArray);
        $caption = $captionArray[0];
        self::assertInstanceOf(RichText::class, $caption);
        self::assertSame('Latin/EA/CS Title ABCאבגDEFァ', $caption->getPlainText());
        $elements = $caption->getRichTextElements();
        self::assertGreaterThan(0, count($elements));
        foreach ($elements as $run) {
            self::assertInstanceOf(Run::class, $run);
            $font = $run->getFont();
            self::assertInstanceOf(Font::class, $font);
            self::assertSame('Times New Roman', $font->getLatin());
            self::assertSame('Malgun Gothic', $font->getEastAsian());
            self::assertSame('Courier New', $font->getComplexScript());
            self::assertEquals(12, $font->getSize());
            self::assertTrue($font->getBold());
            self::assertFalse($font->getItalic());
            self::assertFalse($font->getSuperscript());
            self::assertFalse($font->getSubscript());
            self::assertFalse($font->getStrikethrough());
            self::assertSame('none', $font->getUnderline());
            $chartColor = $font->getChartColor();
            self::assertNotNull($chartColor);
            self::assertSame('000000', $chartColor->getValue());
            self::assertSame('srgbClr', $chartColor->getType());
        }

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[1];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[2];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());
        self::assertSame(7, $values->getPointSize());
        // Had been testing for Fill Color, but we actually
        //  meant to test for marker color, which is now distinct.
        self::assertSame('FFFF00', $values->getMarkerFillColor()->getValue());
        self::assertSame('srgbClr', $values->getMarkerFillColor()->getType());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testScatter8(): void
    {
        $file = self::DIRECTORY . '32readwriteScatterChart8.xlsx';
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
        self::assertSame('Worksheet', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);

        $xAxis = $chart->getChartAxisX();
        self::assertEquals(45, $xAxis->getAxisOptionsProperty('textRotation'));

        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertSame(31750 / Properties::POINTS_WIDTH_MULTIPLIER, $values->getLineWidth());

        self::assertSame('sq', $values->getLineStyleProperty('cap'));
        self::assertSame('tri', $values->getLineStyleProperty('compound'));
        self::assertSame('sysDash', $values->getLineStyleProperty('dash'));
        self::assertSame('miter', $values->getLineStyleProperty('join'));
        self::assertSame('arrow', $values->getLineStyleProperty(['arrow', 'head', 'type']));
        self::assertSame('med', $values->getLineStyleProperty(['arrow', 'head', 'w']));
        self::assertSame('sm', $values->getLineStyleProperty(['arrow', 'head', 'len']));
        self::assertSame('triangle', $values->getLineStyleProperty(['arrow', 'end', 'type']));
        self::assertSame('med', $values->getLineStyleProperty(['arrow', 'end', 'w']));
        self::assertSame('lg', $values->getLineStyleProperty(['arrow', 'end', 'len']));
        self::assertSame('accent1', $values->getLineColorProperty('value'));
        self::assertSame('schemeClr', $values->getLineColorProperty('type'));
        self::assertSame(40, $values->getLineColorProperty('alpha'));
        self::assertSame('', $values->getFillColor());

        self::assertSame(7, $values->getPointSize());
        self::assertSame('diamond', $values->getPointMarker());
        self::assertSame('0070C0', $values->getMarkerFillColor()->getValue());
        self::assertSame('srgbClr', $values->getMarkerFillColor()->getType());
        self::assertSame('002060', $values->getMarkerBorderColor()->getValue());
        self::assertSame('srgbClr', $values->getMarkerBorderColor()->getType());

        $values = $plotValues[1];
        self::assertSame(7, $values->getPointSize());
        self::assertSame('square', $values->getPointMarker());
        self::assertSame('accent6', $values->getMarkerFillColor()->getValue());
        self::assertSame('schemeClr', $values->getMarkerFillColor()->getType());
        self::assertSame(3, $values->getMarkerFillColor()->getAlpha());
        self::assertSame('0FF000', $values->getMarkerBorderColor()->getValue());
        self::assertSame('srgbClr', $values->getMarkerBorderColor()->getType());
        self::assertNull($values->getMarkerBorderColor()->getAlpha());

        $values = $plotValues[2];
        self::assertSame(7, $values->getPointSize());
        self::assertSame('triangle', $values->getPointMarker());
        self::assertSame('FFFF00', $values->getMarkerFillColor()->getValue());
        self::assertSame('srgbClr', $values->getMarkerFillColor()->getType());
        self::assertNull($values->getMarkerFillColor()->getAlpha());
        self::assertSame('accent4', $values->getMarkerBorderColor()->getValue());
        self::assertSame('schemeClr', $values->getMarkerBorderColor()->getType());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testScatter9(): void
    {
        // gradient testing
        $file = self::DIRECTORY . '32readwriteScatterChart9.xlsx';
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
        self::assertSame('Worksheet', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertFalse($chart->getNoFill());
        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        self::assertFalse($plotArea->getNoFill());
        self::assertEquals(315.0, $plotArea->getGradientFillAngle());
        $stops = $plotArea->getGradientFillStops();
        self::assertCount(3, $stops);
        self::assertEquals(0.43808, $stops[0][0]);
        self::assertEquals(0, $stops[1][0]);
        self::assertEquals(0.91, $stops[2][0]);
        $color = $stops[0][1];
        self::assertInstanceOf(ChartColor::class, $color);
        self::assertSame('srgbClr', $color->getType());
        self::assertSame('CDDBEC', $color->getValue());
        self::assertNull($color->getAlpha());
        self::assertSame(20, $color->getBrightness());
        $color = $stops[1][1];
        self::assertInstanceOf(ChartColor::class, $color);
        self::assertSame('srgbClr', $color->getType());
        self::assertSame('FFC000', $color->getValue());
        self::assertNull($color->getAlpha());
        self::assertNull($color->getBrightness());
        $color = $stops[2][1];
        self::assertInstanceOf(ChartColor::class, $color);
        self::assertSame('srgbClr', $color->getType());
        self::assertSame('00B050', $color->getValue());
        self::assertNull($color->getAlpha());
        self::assertSame(4, $color->getBrightness());

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testScatter10(): void
    {
        // nofill for Chart and PlotArea, hidden Axis
        $file = self::DIRECTORY . '32readwriteScatterChart10.xlsx';
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
        self::assertSame('Worksheet', $sheet->getTitle());
        $charts = $sheet->getChartCollection();
        self::assertCount(1, $charts);
        $chart = $charts[0];
        self::assertNotNull($chart);
        self::assertTrue($chart->getNoFill());
        $plotArea = $chart->getPlotArea();
        self::assertNotNull($plotArea);
        self::assertTrue($plotArea->getNoFill());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
