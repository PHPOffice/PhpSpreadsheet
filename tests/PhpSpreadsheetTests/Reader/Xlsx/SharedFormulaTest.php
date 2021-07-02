<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PHPUnit\Framework\TestCase;

class SharedFormulaTest extends TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'samples/templates/32readwriteAreaChart1.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        // confirm that file contains shared formula
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<c r="D6"><f t="shared" ca="1" si="0"/>', $data);
            self::assertStringContainsString('<c r="E6"><f t="shared" ca="1" si="0"/>', $data);
        }
    }

    public function testLoadSheetsXlsxChart(): void
    {
        $filename = self::$testbook;
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename, IReader::LOAD_WITH_CHARTS);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('=(RANDBETWEEN(-50,250)+100)*10', $sheet->getCell('D6')->getValue());
        self::assertSame('=(RANDBETWEEN(-50,250)+100)*10', $sheet->getCell('E6')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
