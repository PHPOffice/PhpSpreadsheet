<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PHPUnit\Framework\TestCase;

class LocaleFloatsTest extends TestCase
{
    /**
     * @var bool
     */
    private $localeAdjusted;

    /**
     * @var false|string
     */
    private $currentLocale;

    protected function setUp(): void
    {
        $this->currentLocale = setlocale(LC_ALL, '0');

        if (!setlocale(LC_ALL, 'fr_FR.UTF-8', 'fra_fra')) {
            $this->localeAdjusted = false;

            return;
        }

        $this->localeAdjusted = true;
    }

    protected function tearDown(): void
    {
        if ($this->localeAdjusted) {
            setlocale(LC_ALL, $this->currentLocale);
        }
    }

    public function testLocaleFloatsCorrectlyConvertedByWriter(): void
    {
        if (!$this->localeAdjusted) {
            self::markTestSkipped('Unable to set locale for testing.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 1.1);

        $filename = 'decimalcomma.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filename);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($filename);
        unlink($filename);

        $result = $spreadsheet->getActiveSheet()->getCell('A1')->getValue();

        ob_start();
        var_dump($result);
        preg_match('/(?:double|float)\(([^\)]+)\)/mui', ob_get_clean(), $matches);
        self::assertArrayHasKey(1, $matches);
        $actual = $matches[1];
        // From PHP8, https://wiki.php.net/rfc/locale_independent_float_to_string applies
        self::assertEquals('1,1', $actual);
    }
}
