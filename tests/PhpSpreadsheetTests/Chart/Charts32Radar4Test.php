<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Charts32Radar4Test extends TestCase
{
    // These tests can only be performed by examining xml.
    // They are based on sample 32readwriteRadarChart4.

    private string $outputFileName = '';

    protected function tearDown(): void
    {
        if ($this->outputFileName !== '') {
            unlink($this->outputFileName);
            $this->outputFileName = '';
        }
    }

    public function test1LummodNoLumoff(): void
    {
        $infile = 'samples/templates/32readwriteRadarChart4.xlsx';
        $reader = new XlsxReader();
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($infile);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $this->outputFileName = File::temporaryFilename();
        $writer->save($this->outputFileName);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFileName;
        $file .= '#xl/charts/chart2.xml';
        $data = file_get_contents($file);
        self::assertNotFalse($data);
        self::assertSame(2, substr_count($data, '<a:lumMod'));
        self::assertSame(2, substr_count($data, '<a:lum'), 'should be no lumOff');
    }
}
