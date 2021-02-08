<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper\NumberFormat;

use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Accounting;
use PHPUnit\Framework\TestCase;

class AccountingWithoutIntlTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (extension_loaded('intl')) {
            self::markTestSkipped('The Intl extension is available, cannot test for fallback');
        }
    }

    /**
     * @group intl
     * @dataProvider accountingMaskWithoutIntlData
     */
    public function testAccountingMaskWithoutIntl(string $expectedResult, ...$args): void
    {
        $AccountingFormatter = new Accounting(...$args);
        $AccountingFormatMask = $AccountingFormatter->format();
        self::assertSame($expectedResult, $AccountingFormatMask);
    }

    public function accountingMaskWithoutIntlData()
    {
        return [
            'Default' => ['[$$-en-US]#,##0.00'],
            'English Language, DefaultCountry' => ['[$$-en-US]#,##0.00', 'en'],
            'English, US, Guess Accounting' => ['[$$-en-US]#,##0.00', 'en_US'],
            'English, UK/GB, Guess Accounting' => ['[$£-en-GB]#,##0.00', 'en_GB'],
            'English, UK/GB, Specify Accounting' => ['[$£-en-GB]#,##0.00', 'en_GB', 'GBP'],
            'English, Canada' => ['[$CA$-en-CA]#,##0.00', 'en_CA', 'CAD'],
            'German, Germany, Guess Accounting)' => ['[$€-de-DE]#,##0.00', 'de_DE'],
        ];
    }
}
