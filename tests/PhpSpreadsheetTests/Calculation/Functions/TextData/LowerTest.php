<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Settings;

class LowerTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLOWER
     *
     * @param mixed $expectedResult
     * @param mixed $str
     */
    public function testLOWER($expectedResult, $str = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=LOWER()');
        } else {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=LOWER(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLOWER(): array
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
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }
        $sheet = $this->getSheet();
        $this->setCell('A1', $value);
        $sheet->getCell('B1')->setValue('=LOWER(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLocaleLOWER(): array
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

    /**
     * @dataProvider providerLowerArray
     */
    public function testLowerArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOWER({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLowerArray(): array
    {
        return [
            'row vector' => [[["let's", 'all change', 'case']], '{"lEt\'S", "aLl chAngE", "cAsE"}'],
            'column vector' => [[["let's"], ['all change'], ['case']], '{"lEt\'S"; "aLl chAngE"; "cAsE"}'],
            'matrix' => [[['build all', 'your workbooks'], ['with', 'phpspreadsheet']], '{"bUIld aLL", "yOUr WOrkBOOks"; "wiTH", "PhpSpreadsheet"}'],
        ];
    }
}
