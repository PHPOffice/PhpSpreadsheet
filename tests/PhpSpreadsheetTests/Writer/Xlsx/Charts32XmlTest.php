<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Charts32XmlTest extends TestCase
{
    // These tests can only be performed by examining xml.
    private const DIRECTORY = 'samples' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    /** @var string */
    private $outputFileName = '';

    protected function tearDown(): void
    {
        if ($this->outputFileName !== '') {
            unlink($this->outputFileName);
            $this->outputFileName = '';
        }
    }

    /**
     * @dataProvider providerScatterCharts
     */
    public function testBezierCount(int $expectedCount, string $inputFile): void
    {
        $file = self::DIRECTORY . $inputFile;
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $this->outputFileName = File::temporaryFilename();
        $writer->save($this->outputFileName);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFileName;
        $file .= '#xl/charts/chart2.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected tags
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertSame(1, substr_count($data, '<c:scatterStyle val='));
            self::assertSame($expectedCount ? 1 : 0, substr_count($data, '<c:scatterStyle val="smoothMarker"/>'));
            self::assertSame($expectedCount, substr_count($data, '<c:smooth val="1"/>'));
        }
    }

    public function providerScatterCharts(): array
    {
        return [
            'no line' => [0, '32readwriteScatterChart1.xlsx'],
            'smooth line (Bezier)' => [3, '32readwriteScatterChart2.xlsx'],
            'straight line' => [0, '32readwriteScatterChart3.xlsx'],
        ];
    }

    public function testAreaPercentageNoCat(): void
    {
        $file = self::DIRECTORY . '32readwriteAreaPercentageChart1.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($file);

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
        } else {
            self::assertSame(0, substr_count($data, '<c:cat>'));
        }
    }
}
