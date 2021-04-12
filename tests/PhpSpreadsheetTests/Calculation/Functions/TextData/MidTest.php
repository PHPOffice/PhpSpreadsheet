<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class MidTest extends TestCase
{
    protected function tearDown(): void
    {
        Settings::setLocale('en_US');
    }

    /**
     * @dataProvider providerMID
     *
     * @param mixed $expectedResult
     */
    public function testMID($expectedResult, ...$args): void
    {
        $result = TextData::MID(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMID(): array
    {
        return require 'tests/data/Calculation/TextData/MID.php';
    }

    /**
     * @dataProvider providerLocaleMID
     *
     * @param string $expectedResult
     * @param mixed $value
     * @param mixed $locale
     * @param mixed $offset
     * @param mixed $characters
     */
    public function testLowerWithLocaleBoolean($expectedResult, $locale, $value, $offset, $characters): void
    {
        $newLocale = Settings::setLocale($locale);
        if ($newLocale === false) {
            Settings::setLocale('en_US');
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }

        $result = TextData::MID($value, $offset, $characters);
        self::assertEquals($expectedResult, $result);

        Settings::setLocale('en_US');
    }

    public function providerLocaleMID(): array
    {
        return [
            ['RA', 'fr_FR', true, 2, 2],
            ['AA', 'nl_NL', true, 2, 2],
            ['OS', 'fi', true, 2, 2],
            ['СТИН', 'bg', true, 2, 4],
            ['AU', 'fr_FR', false, 2, 2],
            ['NWA', 'nl_NL', false, 2, 3],
            ['PÄTO', 'fi', false, 2, 4],
            ['ОЖ', 'bg', false, 2, 2],
        ];
    }
}
