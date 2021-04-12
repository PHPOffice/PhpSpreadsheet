<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class LeftTest extends TestCase
{
    protected function tearDown(): void
    {
        Settings::setLocale('en_US');
    }

    /**
     * @dataProvider providerLEFT
     *
     * @param mixed $expectedResult
     */
    public function testLEFT($expectedResult, ...$args): void
    {
        $result = TextData::LEFT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLEFT(): array
    {
        return require 'tests/data/Calculation/TextData/LEFT.php';
    }

    /**
     * @dataProvider providerLocaleLEFT
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

        $result = TextData::LEFT($value, $characters);
        self::assertEquals($expectedResult, $result);

        Settings::setLocale('en_US');
    }

    public function providerLocaleLEFT(): array
    {
        return [
            ['VR', 'fr_FR', true, 2],
            ['WA', 'nl_NL', true, 2],
            ['TO', 'fi', true, 2],
            ['ИСТ', 'bg', true, 3],
            ['FA', 'fr_FR', false, 2],
            ['ON', 'nl_NL', false, 2],
            ['EPÄT', 'fi', false, 4],
            ['ЛОЖ', 'bg', false, 3],
        ];
    }
}
