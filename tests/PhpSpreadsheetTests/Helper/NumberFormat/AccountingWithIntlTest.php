<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper\NumberFormat;

use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Accounting;
use PHPUnit\Framework\TestCase;

class AccountingWithIntlTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('The Intl extension is not available');
        }
    }

    /**
     * @group intl
     * @dataProvider accountingMaskWithIntlData
     */
    public function testAccountingMaskWithIntl(string $expectedResult, ...$args)
    {
        $currencyFormatter = new Accounting(...$args);
        $currencyFormatMask = $currencyFormatter->format();
        $this->assertSame($expectedResult, $currencyFormatMask);
    }

    public function accountingMaskWithIntlData()
    {
        return [
            'Default' => ['[$$-en-US]#,##0.00;([$$-en-US]#,##0.00)'],
            'English, US (Default)' => ['[$$-en-US]#,##0.00;([$$-en-US]#,##0.00)', 'en_US'],
            'English, UK/GB' => ['[$£-en-GB]#,##0.00;([$£-en-GB]#,##0.00)', 'en_GB'],
            'English, IE, Euros' => ['[$€-en-IE]#,##0.00;([$€-en-IE]#,##0.00)', 'en_IE', 'EUR'],
            'Dutch, Netherlands' => ['[$€-nl-NL] #,##0.00;([$€-nl-NL] #,##0.00)', 'nl_NL'],
            'West Frisian, Netherlands' => ['[$€-fy-NL] #,##0.00;([$€-fy-NL] #,##0.00)', 'fy_NL'],
            'Spanish, Spain' => ['#,##0.00 [$€-es-ES]', 'es_ES'],
            'English, Canada' => ['[$CA$-en-CA]#,##0.00;([$CA$-en-CA]#,##0.00)', 'en_CA'],
            'French, Canada' => ['#,##0.00 [$$CA-fr-CA];(#,##0.00 [$$CA-fr-CA])', 'fr_CA'],
            'English, Canada, US Dollars' => ['[$$-en-CA]#,##0.00;([$$-en-CA]#,##0.00)', 'en_CA', 'USD'],
            'French, Canada, US Dollars' => ['#,##0.00 [$$US-fr-CA];(#,##0.00 [$$US-fr-CA])', 'fr_CA', 'USD'],
            'Pashto, Afghanistan' => ['[$؋-ps-AF]#,##0;([$؋-ps-AF]#,##0)', 'ps_AF'],
        ];
    }
}
