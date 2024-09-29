<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    private string $compatibilityMode;

    private string $returnDate;

    private string $locale;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $this->returnDate = Functions::getReturnDateType();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        $this->locale = Settings::getLocale();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        Functions::setReturnDateType($this->returnDate);
        Settings::setLocale($this->locale);
    }

    /**
     * @dataProvider providerTranslations
     */
    public function testTranslation(string $expectedResult, string $locale, string $formula): void
    {
        $validLocale = Settings::setLocale($locale);
        if (!$validLocale) {
            self::markTestSkipped("Unable to set locale to {$locale}");
        }

        $translatedFormula = Calculation::getInstance()->translateFormulaToLocale($formula);
        self::assertSame($expectedResult, $translatedFormula);

        $restoredFormula = Calculation::getInstance()->translateFormulaToEnglish($translatedFormula);
        self::assertSame(preg_replace(Calculation::CALCULATION_REGEXP_STRIP_XLFN_XLWS, '', $formula), $restoredFormula);
    }

    public static function providerTranslations(): array
    {
        return require 'tests/data/Calculation/Translations.php';
    }
}
