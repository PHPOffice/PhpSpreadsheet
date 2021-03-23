<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class UpperTest extends TestCase
{
    protected function tearDown(): void
    {
        Settings::setLocale('en_US');
    }

    /**
     * @dataProvider providerUPPER
     *
     * @param mixed $expectedResult
     * @param $value
     */
    public function testUPPER($expectedResult, $value): void
    {
        $result = TextData::UPPERCASE($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerUPPER()
    {
        return require 'tests/data/Calculation/TextData/UPPER.php';
    }

    /**
     * @dataProvider providerLocaleLOWER
     *
     * @param string $expectedResult
     * @param $value
     * @param mixed $locale
     */
    public function testLowerWithLocaleBoolean($expectedResult, $locale, $value): void
    {
        $newLocale = Settings::setLocale($locale);
        if ($newLocale === false) {
            Settings::setLocale('en_US');
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }

        $result = TextData::UPPERCASE($value);
        self::assertEquals($expectedResult, $result);

        Settings::setLocale('en_US');
    }

    public function providerLocaleLOWER()
    {
        return [
            ['VRAI', 'fr_FR', true],
            ['WAAR', 'nl_NL', true],
            ['TOSI', 'fi', true],
            ['ИСТИНА', 'bg', true],
            ['FAUX', 'fr_FR', false],
            ['ONWAAR', 'nl_NL', false],
            ['EPÄTOSI', 'fi', false],
            ['ЛОЖЬ', 'bg', false],
        ];
    }
}
