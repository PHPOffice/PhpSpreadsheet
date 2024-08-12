<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class LocaleFloatsTest extends AbstractFunctional
{
    private false|string $currentPhpLocale;

    private string $originalLocale;

    private ?Spreadsheet $spreadsheet = null;

    private ?Spreadsheet $reloadedSpreadsheet = null;

    protected function setUp(): void
    {
        $this->currentPhpLocale = setlocale(LC_ALL, '0');
        $this->originalLocale = Settings::getLocale();
        StringHelper::setDecimalSeparator(null);
        StringHelper::setThousandsSeparator(null);
    }

    protected function tearDown(): void
    {
        StringHelper::setDecimalSeparator(null);
        StringHelper::setThousandsSeparator(null);
        Settings::setLocale($this->originalLocale);
        if ($this->currentPhpLocale !== false) {
            setlocale(LC_ALL, $this->currentPhpLocale);
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

    /**
     * Use separate process because this calls native Php setlocale.
     *
     * @runInSeparateProcess
     */
    public function testLocaleFloatsCorrectlyConvertedByWriter(): void
    {
        if (!setlocale(LC_ALL, 'fr_FR.UTF-8', 'fra_fra.utf8')) {
            $this->currentPhpLocale = false;
            self::markTestSkipped('Unable to set locale for testing.');
        }
        $localeconv = localeconv();
        $decimalSeparator = $localeconv['decimal_point'];
        self::assertNotEquals('.', $decimalSeparator, 'unexpected change to French decimal separator');
        $this->spreadsheet = $spreadsheet = new Spreadsheet();
        $properties = $spreadsheet->getProperties();
        $properties->setCustomProperty('Version', 1.2);
        $spreadsheet->getActiveSheet()->setCellValue('A1', 1.1);

        $this->reloadedSpreadsheet = $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');

        $result = $reloadedSpreadsheet->getActiveSheet()->getCell('A1')->getValue();
        self::assertEqualsWithDelta(1.1, $result, 1.0E-8);
        $prop = $reloadedSpreadsheet->getProperties()->getCustomPropertyValue('Version');
        self::assertEqualsWithDelta(1.2, $prop, 1.0E-8);

        $actual = $reloadedSpreadsheet->getActiveSheet()->getCell('A1')->getFormattedValue();
        self::assertStringContainsString("1{$decimalSeparator}1", $actual);
    }

    public function testPercentageStoredAsString(): void
    {
        Settings::setLocale('fr_FR');
        StringHelper::setDecimalSeparator(',');
        StringHelper::setThousandsSeparator('.');
        $reader = new XlsxReader();
        $this->spreadsheet = $spreadsheet = $reader->load('tests/data/Writer/XLSX/issue.3811b.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('48,34%', $sheet->getCell('L2')->getValue());
        self::assertIsString($sheet->getCell('L2')->getValue());
        self::assertSame('=(10%+L2)/2', $sheet->getCell('L1')->getValue());
        self::assertEqualsWithDelta(0.2917, $sheet->getCell('L1')->getCalculatedValue(), 1E-8);
        self::assertIsFloat($sheet->getCell('L1')->getCalculatedValue());
        self::assertEquals('29,17%', $sheet->getCell('L1')->getFormattedValue());

        $sheet->getCell('A10')->setValue(3.2);
        self::assertSame(NumberFormat::FORMAT_GENERAL, $sheet->getStyle('A10')->getNumberFormat()->getFormatCode());
        self::assertSame('3,2', $sheet->getCell('A10')->getFormattedValue());
        $sheet->getCell('A11')->setValue(1002.5);
        $sheet->getStyle('A11')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        self::assertSame('1.002,50', $sheet->getCell('A11')->getFormattedValue());
        $sheet->getCell('A12')->setValue(2.5);
        $sheet->getStyle('A12')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        self::assertSame('2,50', $sheet->getCell('A12')->getFormattedValue());
    }

    /**
     * Use separate process because this calls native Php setlocale.
     *
     * @runInSeparateProcess
     */
    public function testPercentageStoredAsString2(): void
    {
        if (!setlocale(LC_ALL, 'fr_FR.UTF-8', 'fra_fra.utf8')) {
            $this->currentPhpLocale = false;
            self::markTestSkipped('Unable to set locale for testing.');
        }
        $localeconv = localeconv();
        $thousandsSeparator = $localeconv['thousands_sep'];
        $decimalSeparator = $localeconv['decimal_point'];
        self::assertNotEquals('.', $decimalSeparator, 'unexpected change to French decimal separator');
        self::assertNotEquals(',', $thousandsSeparator, 'unexpected change to French thousands separator');
        $reader = new XlsxReader();
        $this->spreadsheet = $spreadsheet = $reader->load('tests/data/Writer/XLSX/issue.3811b.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('48,34%', $sheet->getCell('L2')->getValue());
        self::assertIsString($sheet->getCell('L2')->getValue());
        self::assertSame('=(10%+L2)/2', $sheet->getCell('L1')->getValue());
        self::assertEqualsWithDelta(0.2917, $sheet->getCell('L1')->getCalculatedValue(), 1E-8);
        self::assertIsFloat($sheet->getCell('L1')->getCalculatedValue());
        self::assertSame("29{$decimalSeparator}17%", $sheet->getCell('L1')->getFormattedValue());

        $sheet->getCell('A10')->setValue(3.2);
        self::assertSame(NumberFormat::FORMAT_GENERAL, $sheet->getStyle('A10')->getNumberFormat()->getFormatCode());
        self::assertSame("3{$decimalSeparator}2", $sheet->getCell('A10')->getFormattedValue());
        $sheet->getCell('A11')->setValue(1002.5);
        $sheet->getStyle('A11')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        self::assertSame("1{$thousandsSeparator}002{$decimalSeparator}50", $sheet->getCell('A11')->getFormattedValue());
        $sheet->getCell('A12')->setValue(2.5);
        $sheet->getStyle('A12')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        self::assertSame("2{$decimalSeparator}50", $sheet->getCell('A12')->getFormattedValue());
    }
}
