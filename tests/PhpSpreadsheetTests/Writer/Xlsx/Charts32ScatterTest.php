<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

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
        self::assertSame('000000', $font->getColor()->getRGB());

        $plotArea = $chart->getPlotArea();
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[1];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[2];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(7, $values->getPointSize());
        self::assertSame('FFFF00', $values->getFillColor());

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
        self::assertSame('000000', $font->getColor()->getRGB());

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
        self::assertSame('00B0F0', $font->getColor()->getRGB());

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
        self::assertSame('000000', $font->getColor()->getRGB());

        $plotArea = $chart->getPlotArea();
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[1];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[2];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(7, $values->getPointSize());
        self::assertSame('FFFF00', $values->getFillColor());

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
        self::assertSame('000000', $font->getColor()->getRGB());

        $plotArea = $chart->getPlotArea();
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertTrue($values->getScatterLines());
        self::assertSame(12700, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[1];
        self::assertTrue($values->getScatterLines());
        self::assertSame(12700, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[2];
        self::assertTrue($values->getScatterLines());
        self::assertSame(12700, $values->getLineWidth());
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
            self::assertSame('000000', $font->getColor()->getRGB());
        }

        $plotArea = $chart->getPlotArea();
        $plotSeries = $plotArea->getPlotGroup();
        self::assertCount(1, $plotSeries);
        $dataSeries = $plotSeries[0];
        $plotValues = $dataSeries->getPlotValues();
        self::assertCount(3, $plotValues);
        $values = $plotValues[0];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[1];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(3, $values->getPointSize());
        self::assertSame('', $values->getFillColor());
        $values = $plotValues[2];
        self::assertFalse($values->getScatterLines());
        self::assertSame(28575, $values->getLineWidth());
        self::assertSame(7, $values->getPointSize());
        self::assertSame('FFFF00', $values->getFillColor());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
