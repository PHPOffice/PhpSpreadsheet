<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Settings;

class UpperTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerUPPER
     */
    public function testUPPER(mixed $expectedResult, mixed $str = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=UPPER()');
        } else {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=UPPER(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerUPPER(): array
    {
        return require 'tests/data/Calculation/TextData/UPPER.php';
    }

    /**
     * @dataProvider providerLocaleLOWER
     */
    public function testLowerWithLocaleBoolean(string $expectedResult, string $locale, mixed $value): void
    {
        $newLocale = Settings::setLocale($locale);
        if ($newLocale === false) {
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }
        $sheet = $this->getSheet();
        $this->setCell('A1', $value);
        $sheet->getCell('B1')->setValue('=UPPER(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLocaleLOWER(): array
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

    /**
     * @dataProvider providerUpperArray
     */
    public function testUpperArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=UPPER({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerUpperArray(): array
    {
        return [
            'row vector' => [[["LET'S", 'ALL CHANGE', 'CASE']], '{"lEt\'S", "aLl chAngE", "cAsE"}'],
            'column vector' => [[["LET'S"], ['ALL CHANGE'], ['CASE']], '{"lEt\'S"; "aLl chAngE"; "cAsE"}'],
            'matrix' => [[['BUILD ALL', 'YOUR WORKBOOKS'], ['WITH', 'PHPSPREADSHEET']], '{"bUIld aLL", "yOUr WOrkBOOks"; "wiTH", "PhpSpreadsheet"}'],
        ];
    }
}
