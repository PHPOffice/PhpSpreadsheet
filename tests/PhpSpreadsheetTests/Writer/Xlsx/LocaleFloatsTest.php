<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class LocaleFloatsTest extends AbstractFunctional
{
    /**
     * @var bool
     */
    private $localeAdjusted;

    /**
     * @var false|string
     */
    private $currentLocale;

    /** @var ?Spreadsheet */
    private $spreadsheet;

    /** @var ?Spreadsheet */
    private $reloadedSpreadsheet;

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
        if ($this->localeAdjusted && is_string($this->currentLocale)) {
            setlocale(LC_ALL, $this->currentLocale);
        }
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->reloadedSpreadsheet !== null) {
            $this->reloadedSpreadsheet->disconnectWorksheets();
            $this->reloadedSpreadsheet = null;
        }
    }

    public function testLocaleFloatsCorrectlyConvertedByWriter(): void
    {
        if (!$this->localeAdjusted) {
            self::markTestSkipped('Unable to set locale for testing.');
        }

        $this->spreadsheet = $spreadsheet = new Spreadsheet();
        $properties = $spreadsheet->getProperties();
        $properties->setCustomProperty('Version', 1.2);
        $spreadsheet->getActiveSheet()->setCellValue('A1', 1.1);

        $this->reloadedSpreadsheet = $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');

        $result = $reloadedSpreadsheet->getActiveSheet()->getCell('A1')->getValue();
        self::assertEqualsWithDelta(1.1, $result, 1.0E-8);
        $prop = $reloadedSpreadsheet->getProperties()->getCustomPropertyValue('Version');
        self::assertEqualsWithDelta(1.2, $prop, 1.0E-8);

        $actual = sprintf('%f', $result);
        self::assertStringContainsString('1,1', $actual);
    }
}
