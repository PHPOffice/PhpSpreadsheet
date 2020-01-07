<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PHPUnit\Framework\TestCase;

class LocaleFloatsTest extends TestCase
{
    protected $localeAdjusted;

    protected $currentLocale;

    public function setUp()
    {
        $this->currentLocale = setlocale(LC_ALL, '0');

        if (!setlocale(LC_ALL, 'fr_FR.UTF-8')) {
            $this->localeAdjusted = false;

            return;
        }

        $this->localeAdjusted = true;
    }

    public function tearDown()
    {
        if ($this->localeAdjusted) {
            setlocale(LC_ALL, $this->currentLocale);
        }
    }

    public function testLocaleFloatsCorrectlyConvertedByWriter()
    {
        if (!$this->localeAdjusted) {
            $this->markTestSkipped('Unable to set locale for testing.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 1.1);

        $filename = 'decimalcomma.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filename);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($filename);

        $result = $spreadsheet->getActiveSheet()->getCell('A1')->getValue();

        ob_start();
        var_dump($result);
        preg_match('/(?:double|float)\(([^\)]+)\)/mui', ob_get_clean(), $matches);
        $this->assertArrayHasKey(1, $matches);
        $actual = $matches[1];
        $this->assertEquals('1,1', $actual);
    }
}
