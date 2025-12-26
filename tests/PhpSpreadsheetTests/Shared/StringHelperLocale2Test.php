<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringHelperLocale2Test extends TestCase
{
    private Spreadsheet $spreadsheet;

    protected function tearDown(): void
    {
        StringHelper::setLocale(null);
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    #[DataProvider('providerIntlLocales')]
    public function testIntlLocales(bool|string $expectedResult, string $expectedTrue, ?string $locale): void
    {
        $this->spreadsheet = new Spreadsheet();
        $number = 12345.67;
        $numberFormat = '[$]#,##0.0';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[$number]]);
        $sheet->getStyle('A1')->getNumberFormat()
            ->setFormatCode($numberFormat);
        $success = StringHelper::setLocale($locale);
        if ($success === false) {
            self::assertFalse($expectedResult);
        } else {
            $result = $sheet->getCell('A1')->getFormattedValue();
            self::assertSame($expectedResult, $result);
            self::assertSame($expectedTrue, Calculation::getTRUE());
        }
    }

    /**
     * The data here are somewhat volatile.
     * They may change with any new release of ICU.
     */
    public static function providerIntlLocales(): array
    {
        $nbsp = "\u{A0}";
        $nnbsp = "\u{202F}";
        $rsquo = "\u{2019}";

        return [
            [false, 'TRUE', 'unknownlocale'],
            ["¤12{$nbsp}345,7", 'SANT', 'sv'],
            ['$12,345.7', 'TRUE', 'en_US'],
            'lowercase country' => ["€12{$nnbsp}345,7", 'VRAI', 'fr_fr'],
            'reset if locale is null' => ['$12,345.7', 'TRUE', null],
            ["CHF12{$rsquo}345.7", 'VERO', 'it_CH'],
            'ignore utf-8' => ['د.ك.‏12٬345٫7', 'TRUE', 'ar_KW.UTF-8'],
            'non-Latin' => ["₽12{$nbsp}345,7", 'ИСТИНА', 'ru_ru'],
        ];
    }

    public function testNoIntl(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $number = 12345.67;
        $numberFormat = '[$]#,##0.0';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([[$number]]);
        $sheet->getStyle('A1')->getNumberFormat()
            ->setFormatCode($numberFormat);
        $success = StringHelperNoIntl::setLocale('en_US');
        self::assertFalse($success);
    }
}
