<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    /**
     * @var string
     */
    private $returnDate;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $this->returnDate = Functions::getReturnDateType();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        Functions::setReturnDateType($this->returnDate);
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

        $translatedFormula = Calculation::getInstance()->_translateFormulaToLocale($formula);
        self::assertSame($expectedResult, $translatedFormula);

        $restoredFormula = Calculation::getInstance()->_translateFormulaToEnglish($translatedFormula);
        self::assertSame($formula, $restoredFormula);
    }

    public function providerTranslations(): array
    {
        return require 'tests/data/Calculation/Translations.php';
    }
}
