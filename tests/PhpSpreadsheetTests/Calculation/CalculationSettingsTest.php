<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class CalculationSettingsTest extends TestCase
{
    private string $compatibilityMode;

    private string $locale;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $calculation = Calculation::getInstance();
        $this->locale = $calculation->getLocale();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        $calculation = Calculation::getInstance();
        $calculation->setLocale($this->locale);
    }

    /**
     * @dataProvider providerCanLoadAllSupportedLocales
     */
    public function testCanLoadAllSupportedLocales(string $locale): void
    {
        $calculation = Calculation::getInstance();
        self::assertTrue($calculation->setLocale($locale));
    }

    public function testInvalidLocaleReturnsFalse(): void
    {
        $calculation = Calculation::getInstance();
        self::assertFalse($calculation->setLocale('xx'));
    }

    public static function providerCanLoadAllSupportedLocales(): array
    {
        return [
            ['bg'],
            ['cs'],
            ['da'],
            ['de'],
            ['en_us'],
            ['es'],
            ['fi'],
            ['fr'],
            ['hu'],
            ['it'],
            ['nl'],
            ['nb'],
            ['pl'],
            ['pt'],
            ['pt_br'],
            ['ru'],
            ['sv'],
            ['tr'],
        ];
    }
}
