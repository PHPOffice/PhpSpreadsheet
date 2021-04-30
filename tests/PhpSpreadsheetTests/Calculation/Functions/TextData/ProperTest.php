<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class ProperTest extends TestCase
{
    protected function tearDown(): void
    {
        Settings::setLocale('en_US');
    }

    /**
     * @dataProvider providerPROPER
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testPROPER($expectedResult, $value): void
    {
        $result = TextData::PROPERCASE($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerPROPER(): array
    {
        return require 'tests/data/Calculation/TextData/PROPER.php';
    }

    /**
     * @dataProvider providerLocaleLOWER
     *
     * @param string $expectedResult
     * @param mixed $value
     * @param mixed $locale
     */
    public function testLowerWithLocaleBoolean($expectedResult, $locale, $value): void
    {
        $newLocale = Settings::setLocale($locale);
        if ($newLocale === false) {
            Settings::setLocale('en_US');
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }

        $result = TextData::PROPERCASE($value);
        self::assertEquals($expectedResult, $result);

        Settings::setLocale('en_US');
    }

    public function providerLocaleLOWER(): array
    {
        return [
            ['Vrai', 'fr_FR', true],
            ['Waar', 'nl_NL', true],
            ['Tosi', 'fi', true],
            ['Истина', 'bg', true],
            ['Faux', 'fr_FR', false],
            ['Onwaar', 'nl_NL', false],
            ['Epätosi', 'fi', false],
            ['Ложь', 'bg', false],
        ];
    }
}
