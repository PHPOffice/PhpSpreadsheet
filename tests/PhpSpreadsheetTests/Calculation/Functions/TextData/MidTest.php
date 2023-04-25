<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Settings;

class MidTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMID
     *
     * @param mixed $expectedResult
     * @param mixed $str string from which to extract
     * @param mixed $start position at which to start
     * @param mixed $cnt number of characters to extract
     */
    public function testMID($expectedResult, $str = 'omitted', $start = 'omitted', $cnt = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=MID()');
        } elseif ($start === 'omitted') {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=MID(A1)');
        } elseif ($cnt === 'omitted') {
            $this->setCell('A1', $str);
            $this->setCell('A2', $start);
            $sheet->getCell('B1')->setValue('=MID(A1, A2)');
        } else {
            $this->setCell('A1', $str);
            $this->setCell('A2', $start);
            $this->setCell('A3', $cnt);
            $sheet->getCell('B1')->setValue('=MID(A1, A2, A3)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerMID(): array
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
    public function testMiddleWithLocaleBoolean($expectedResult, $locale, $value, $offset, $characters): void
    {
        $newLocale = Settings::setLocale($locale);
        if ($newLocale === false) {
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }

        $sheet = $this->getSheet();
        $this->setCell('A1', $value);
        $this->setCell('A2', $offset);
        $this->setCell('A3', $characters);
        $sheet->getCell('B1')->setValue('=MID(A1, A2, A3)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLocaleMID(): array
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

    /**
     * @dataProvider providerCalculationTypeMIDTrue
     */
    public function testCalculationTypeTrue(string $type, string $resultB1, string $resultB2, string $resultB3): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A1', true);
        $this->setCell('A2', 'hello');
        $this->setCell('B1', '=MID(A1, 3, 1)');
        $this->setCell('B2', '=MID(A2, A1, 1)');
        $this->setCell('B3', '=MID(A2, 2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
        self::assertEquals($resultB3, $sheet->getCell('B3')->getCalculatedValue());
    }

    public static function providerCalculationTypeMIDTrue(): array
    {
        return [
            'Excel MID(true,3,1), MID("hello",true, 1), MID("hello", 2, true)' => [
                Functions::COMPATIBILITY_EXCEL,
                'U',
                'h',
                'e',
            ],
            'Gnumeric MID(true,3,1), MID("hello",true, 1), MID("hello", 2, true)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                'U',
                'h',
                'e',
            ],
            'OpenOffice MID(true,3,1), MID("hello",true, 1), MID("hello", 2, true)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '',
                '#VALUE!',
                '#VALUE!',
            ],
        ];
    }

    /**
     * @dataProvider providerCalculationTypeMIDFalse
     */
    public function testCalculationTypeFalse(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A1', false);
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=MID(A1, 3, 1)');
        $this->setCell('B2', '=MID(A2, A1, 1)');
        $this->setCell('B3', '=MID(A2, 2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public static function providerCalculationTypeMIDFalse(): array
    {
        return [
            'Excel MID(false,3,1), MID("hello", false, 1), MID("hello", 2, false)' => [
                Functions::COMPATIBILITY_EXCEL,
                'L',
                '#VALUE!',
                '',
            ],
            'Gnumeric MID(false,3,1), MID("hello", false, 1), MID("hello", 2, false)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                'L',
                '#VALUE!',
                '',
            ],
            'OpenOffice MID(false,3,1), MID("hello", false, 1), MID("hello", 2, false)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '',
                '#VALUE!',
                '#VALUE!',
            ],
        ];
    }

    /**
     * @dataProvider providerCalculationTypeMIDNull
     */
    public function testCalculationTypeNull(string $type, string $resultB1, string $resultB2, string $resultB3): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=MID(A1, 3, 1)');
        $this->setCell('B2', '=MID(A2, A1, 1)');
        $this->setCell('B3', '=MID(A2, 2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
        self::assertEquals($resultB3, $sheet->getCell('B3')->getCalculatedValue());
    }

    public static function providerCalculationTypeMIDNull(): array
    {
        return [
            'Excel MID(null,3,1), MID("hello", null, 1), MID("hello", 2, null)' => [
                Functions::COMPATIBILITY_EXCEL,
                '',
                '#VALUE!',
                '',
            ],
            'Gnumeric MID(null,3,1), MID("hello", null, 1), MID("hello", 2, null)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                '',
                '#VALUE!',
                '',
            ],
            'OpenOffice MID(null,3,1), MID("hello", null, 1), MID("hello", 2, null)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '',
                '#VALUE!',
                '',
            ],
        ];
    }

    /**
     * @dataProvider providerMidArray
     */
    public function testMidArray(array $expectedResult, string $argument1, string $argument2, string $argument3): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=MID({$argument1}, {$argument2}, {$argument3})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerMidArray(): array
    {
        return [
            'row vector #1' => [[['lo Wor', 'Spread']], '{"Hello World", "PhpSpreadsheet"}', '4', '6'],
            'column vector #1' => [[[' Wor'], ['read']], '{"Hello World"; "PhpSpreadsheet"}', '6', '4'],
        ];
    }
}
