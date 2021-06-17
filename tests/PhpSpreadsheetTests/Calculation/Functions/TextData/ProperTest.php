<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Settings;

class ProperTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPROPER
     *
     * @param mixed $expectedResult
     * @param mixed $str
     */
    public function testPROPER($expectedResult, $str = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=PROPER()');
        } else {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=PROPER(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerPROPER(): array
    {
        return require 'tests/data/Calculation/TextData/PROPER.php';
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
        $sheet->getCell('B1')->setValue('=PROPER(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerLocaleLOWER(): array
    {
        return [
            ['Vrai', 'fr_FR', true],
            ['Waar', 'nl_NL', true],
            ['Tosi', 'fi', true],
            ['Истина', 'bg', true],
            ['Faux', 'fr_FR', false],
            ['Onwaar', 'nl_NL', false],
            ['Epätosi', 'fi', false],
            ['Ложь', 'bg', false],
        ];
    }
}
