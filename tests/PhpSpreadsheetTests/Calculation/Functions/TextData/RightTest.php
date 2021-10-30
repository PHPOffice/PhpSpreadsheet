<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Settings;

class RightTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRIGHT
     *
     * @param mixed $expectedResult
     * @param mixed $str string from which to extract
     * @param mixed $cnt number of characters to extract
     */
    public function testRIGHT($expectedResult, $str = 'omitted', $cnt = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=RIGHT()');
        } elseif ($cnt === 'omitted') {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=RIGHT(A1)');
        } else {
            $this->setCell('A1', $str);
            $this->setCell('A2', $cnt);
            $sheet->getCell('B1')->setValue('=RIGHT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
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
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }

        $sheet = $this->getSheet();
        $this->setCell('A1', $value);
        $this->setCell('A2', $characters);
        $sheet->getCell('B1')->setValue('=RIGHT(A1, A2)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
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

    /**
     * @dataProvider providerCalculationTypeRIGHTTrue
     */
    public function testCalculationTypeTrue(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A1', true);
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=RIGHT(A1, 1)');
        $this->setCell('B2', '=RIGHT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public function providerCalculationTypeRIGHTTrue(): array
    {
        return [
            'Excel RIGHT(true, 1) AND RIGHT("hello", true)' => [
                Functions::COMPATIBILITY_EXCEL,
                'E',
                'o',
            ],
            'Gnumeric RIGHT(true, 1) AND RIGHT("hello", true)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                'E',
                'o',
            ],
            'OpenOffice RIGHT(true, 1) AND RIGHT("hello", true)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '1',
                '#VALUE!',
            ],
        ];
    }

    /**
     * @dataProvider providerCalculationTypeRIGHTFalse
     */
    public function testCalculationTypeFalse(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A1', false);
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=RIGHT(A1, 1)');
        $this->setCell('B2', '=RIGHT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public function providerCalculationTypeRIGHTFalse(): array
    {
        return [
            'Excel RIGHT(false, 1) AND RIGHT("hello", false)' => [
                Functions::COMPATIBILITY_EXCEL,
                'E',
                '',
            ],
            'Gnumeric RIGHT(false, 1) AND RIGHT("hello", false)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                'E',
                '',
            ],
            'OpenOffice RIGHT(false, 1) AND RIGHT("hello", false)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '0',
                '#VALUE!',
            ],
        ];
    }

    /**
     * @dataProvider providerCalculationTypeRIGHTNull
     */
    public function testCalculationTypeNull(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=RIGHT(A1, 1)');
        $this->setCell('B2', '=RIGHT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public function providerCalculationTypeRIGHTNull(): array
    {
        return [
            'Excel RIGHT(null, 1) AND RIGHT("hello", null)' => [
                Functions::COMPATIBILITY_EXCEL,
                '',
                '',
            ],
            'Gnumeric RIGHT(null, 1) AND RIGHT("hello", null)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                '',
                'o',
            ],
            'OpenOffice RIGHT(null, 1) AND RIGHT("hello", null)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '',
                '',
            ],
        ];
    }
}
