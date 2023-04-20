<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Settings;

class AddressInternationalTest extends AllSetupTeardown
{
    /** @var string */
    private $locale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locale = Settings::getLocale();
    }

    protected function tearDown(): void
    {
        Settings::setLocale($this->locale);
        // CompatibilityMode is restored in parent
        parent::tearDown();
    }

    /**
     * @dataProvider providerInternational
     */
    public function testR1C1International(string $locale, string $r, string $c): void
    {
        if ($locale !== '') {
            Settings::setLocale($locale);
        }
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=LEFT(ADDRESS(1,1,1,0),1)');
        $sheet->getCell('A2')->setValue('=MID(ADDRESS(1,1,1,0),3,1)');
        self::assertSame($r, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame($c, $sheet->getCell('A2')->getCalculatedValue());
    }

    public static function providerInternational(): array
    {
        return [
            'Default' => ['', 'R', 'C'],
            'English' => ['en', 'R', 'C'],
            'French' => ['fr', 'L', 'C'],
            'German' => ['de', 'Z', 'S'],
            'Made-up' => ['xx', 'R', 'C'],
            'Spanish' => ['es', 'F', 'C'],
            'Bulgarian' => ['bg', 'R', 'C'],
            'Czech' => ['cs', 'R', 'C'], // maybe should be R/S
            'Polish' => ['pl', 'R', 'C'], // maybe should be W/K
            'Turkish' => ['tr', 'R', 'C'],
        ];
    }

    /**
     * @dataProvider providerCompatibility
     */
    public function testCompatibilityInternational(string $compatibilityMode, string $r, string $c): void
    {
        Functions::setCompatibilityMode($compatibilityMode);
        Settings::setLocale('de');
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=LEFT(ADDRESS(1,1,1,0),1)');
        $sheet->getCell('A2')->setValue('=MID(ADDRESS(1,1,1,0),3,1)');
        self::assertSame($r, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame($c, $sheet->getCell('A2')->getCalculatedValue());
    }

    public static function providerCompatibility(): array
    {
        return [
            [Functions::COMPATIBILITY_EXCEL, 'Z', 'S'],
            [Functions::COMPATIBILITY_OPENOFFICE, 'R', 'C'],
            [Functions::COMPATIBILITY_GNUMERIC, 'R', 'C'],
        ];
    }
}
