<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Settings;

class UpperTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerUPPER
     *
     * @param mixed $expectedResult
     * @param mixed $str
     */
    public function testUPPER($expectedResult, $str = 'omitted'): void
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

    public function providerUPPER(): array
    {
        return require 'tests/data/Calculation/TextData/UPPER.php';
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
        $sheet->getCell('B1')->setValue('=UPPER(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerLocaleLOWER(): array
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
}
