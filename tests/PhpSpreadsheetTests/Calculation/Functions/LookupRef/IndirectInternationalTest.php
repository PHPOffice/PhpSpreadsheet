<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Settings;

class IndirectInternationalTest extends AllSetupTeardown
{
    private string $locale;

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
    public function testR1C1International(string $locale): void
    {
        Settings::setLocale($locale);
        $sameAsEnglish = ['en', 'xx', 'ru', 'tr', 'cs', 'pl'];
        $sheet = $this->getSheet();
        $sheet->getCell('C1')->setValue('text');
        $sheet->getCell('A2')->setValue('en');
        $sheet->getCell('B2')->setValue('=INDIRECT("R1C3", false)');
        $sheet->getCell('A3')->setValue('fr');
        $sheet->getCell('B3')->setValue('=INDIRECT("L1C3", false)');
        $sheet->getCell('A4')->setValue('de');
        $sheet->getCell('B4')->setValue('=INDIRECT("Z1S3", false)');
        $sheet->getCell('A5')->setValue('es');
        $sheet->getCell('B5')->setValue('=INDIRECT("F1C3", false)');
        $sheet->getCell('A6')->setValue('xx');
        $sheet->getCell('B6')->setValue('=INDIRECT("R1C3", false)');
        $sheet->getCell('A7')->setValue('ru');
        $sheet->getCell('B7')->setValue('=INDIRECT("R1C3", false)');
        $sheet->getCell('A8')->setValue('cs');
        $sheet->getCell('B8')->setValue('=INDIRECT("R1C3", false)');
        $sheet->getCell('A9')->setValue('tr');
        $sheet->getCell('B9')->setValue('=INDIRECT("R1C3", false)');
        $sheet->getCell('A10')->setValue('pl');
        $sheet->getCell('B10')->setValue('=INDIRECT("R1C3", false)');
        $maxRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $maxRow; ++$row) {
            /** @var null|bool|float|int|string */
            $rowLocale = $sheet->getCell("A$row")->getValue();
            if (in_array($rowLocale, $sameAsEnglish, true) && in_array($locale, $sameAsEnglish, true)) {
                $expectedResult = 'text';
            } else {
                $expectedResult = ($locale === $sheet->getCell("A$row")->getValue()) ? 'text' : '#REF!';
            }
            self::assertSame($expectedResult, $sheet->getCell("B$row")->getCalculatedValue(), "Locale $locale error in cell B$row $rowLocale");
        }
    }

    public static function providerInternational(): array
    {
        return [
            'English' => ['en'],
            'French' => ['fr'],
            'German' => ['de'],
            'Made-up' => ['xx'],
            'Spanish' => ['es'],
            'Russian' => ['ru'],
            'Czech' => ['cs'],
            'Polish' => ['pl'],
            'Turkish' => ['tr'],
        ];
    }

    /**
     * @dataProvider providerRelativeInternational
     */
    public function testRelativeInternational(string $locale, string $cell, string $relative): void
    {
        Settings::setLocale($locale);
        $sheet = $this->getSheet();
        $sheet->getCell('C3')->setValue('text');
        $sheet->getCell($cell)->setValue("=INDIRECT(\"$relative\", false)");
        self::assertSame('text', $sheet->getCell($cell)->getCalculatedValue());
    }

    public static function providerRelativeInternational(): array
    {
        return [
            'English A3' => ['en', 'A3', 'R[]C[+2]'],
            'French B4' => ['fr', 'B4', 'L[-1]C[+1]'],
            'German C5' => ['de', 'C5', 'Z[-2]S[]'],
            'Spanish E1' => ['es', 'E1', 'F[+2]C[-2]'],
        ];
    }

    /**
     * @dataProvider providerCompatibility
     */
    public function testCompatibilityInternational(string $compatibilityMode): void
    {
        Functions::setCompatibilityMode($compatibilityMode);
        if ($compatibilityMode === Functions::COMPATIBILITY_EXCEL) {
            $expected1 = '#REF!';
            $expected2 = 'text';
        } else {
            $expected2 = '#REF!';
            $expected1 = 'text';
        }
        Settings::setLocale('fr');
        $sheet = $this->getSheet();
        $sheet->getCell('C3')->setValue('text');
        $sheet->getCell('A1')->setValue('=INDIRECT("R3C3", false)');
        $sheet->getCell('A2')->setValue('=INDIRECT("L3C3", false)');
        self::assertSame($expected1, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame($expected2, $sheet->getCell('A2')->getCalculatedValue());
    }

    public static function providerCompatibility(): array
    {
        return [
            [Functions::COMPATIBILITY_EXCEL],
            [Functions::COMPATIBILITY_OPENOFFICE],
            [Functions::COMPATIBILITY_GNUMERIC],
        ];
    }
}
