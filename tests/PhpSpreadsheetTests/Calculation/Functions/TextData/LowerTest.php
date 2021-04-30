<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class LowerTest extends TestCase
{
    protected function tearDown(): void
    {
        Settings::setLocale('en_US');
    }

    /**
     * @dataProvider providerLOWER
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testLOWER($expectedResult, $value): void
    {
        $result = TextData::LOWERCASE($value);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLOWER(): array
    {
        return require 'tests/data/Calculation/TextData/LOWER.php';
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

        $result = TextData::LOWERCASE($value);
        self::assertEquals($expectedResult, $result);

        Settings::setLocale('en_US');
    }

    public function providerLocaleLOWER(): array
    {
        return [
            ['vrai', 'fr_FR', true],
            ['waar', 'nl_NL', true],
            ['tosi', 'fi', true],
            ['истина', 'bg', true],
            ['faux', 'fr_FR', false],
            ['onwaar', 'nl_NL', false],
            ['epätosi', 'fi', false],
            ['ложь', 'bg', false],
        ];
    }
}
