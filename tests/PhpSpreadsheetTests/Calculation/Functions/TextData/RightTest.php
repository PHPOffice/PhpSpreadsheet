<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class RightTest extends TestCase
{
    protected function tearDown(): void
    {
        Settings::setLocale('en_US');
    }

    /**
     * @dataProvider providerRIGHT
     *
     * @param mixed $expectedResult
     */
    public function testRIGHT($expectedResult, ...$args): void
    {
        $result = TextData::RIGHT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerRIGHT(): array
    {
        return require 'tests/data/Calculation/TextData/RIGHT.php';
    }

    /**
     * @dataProvider providerLocaleRIGHT
     *
     * @param string $expectedResult
     * @param mixed $value
     * @param mixed $locale
     * @param mixed $characters
     */
    public function testLowerWithLocaleBoolean($expectedResult, $locale, $value, $characters): void
    {
        $newLocale = Settings::setLocale($locale);
        if ($newLocale === false) {
            Settings::setLocale('en_US');
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }

        $result = TextData::RIGHT($value, $characters);
        self::assertEquals($expectedResult, $result);

        Settings::setLocale('en_US');
    }

    public function providerLocaleRIGHT(): array
    {
        return [
            ['RAI', 'fr_FR', true, 3],
            ['AAR', 'nl_NL', true, 3],
            ['OSI', 'fi', true, 3],
            ['ИНА', 'bg', true, 3],
            ['UX', 'fr_FR', false, 2],
            ['WAAR', 'nl_NL', false, 4],
            ['ÄTOSI', 'fi', false, 5],
            ['ЖЬ', 'bg', false, 2],
        ];
    }
}
