<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DGetTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDGet
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDGet($expectedResult, $database, $field, $criteria): void
    {
        $result = Database::DGET($database, $field, $criteria);
        self::assertSame($expectedResult, $result);
    }

    private function database1(): array
    {
        return [
            ['Tree', 'Height', 'Age', 'Yield', 'Profit'],
            ['Apple', 18, 20, 14, 105],
            ['Pear', 12, 12, 10, 96],
            ['Cherry', 13, 14, 9, 105],
            ['Apple', 14, 15, 10, 75],
            ['Pear', 9, 8, 8, 77],
            ['Apple', 8, 9, 6, 45],
        ];
    }

    private function database2(): array
    {
        return [
            ['Quarter', 'Area', 'Sales Rep.', 'Sales'],
            [1, 'North', 'Jeff', 223000],
            [1, 'North', 'Chris', 125000],
            [1, 'South', 'Carol', 456000],
            [1, 'South', 'Tina', 289000],
            [2, 'North', 'Jeff', 322000],
            [2, 'North', 'Chris', 340000],
            [2, 'South', 'Carol', 198000],
            [2, 'South', 'Tina', 222000],
            [3, 'North', 'Jeff', 310000],
            [3, 'North', 'Chris', 250000],
            [3, 'South', 'Carol', 460000],
            [3, 'South', 'Tina', 395000],
            [4, 'North', 'Jeff', 261000],
            [4, 'North', 'Chris', 389000],
            [4, 'South', 'Carol', 305000],
            [4, 'South', 'Tina', 188000],
        ];
    }

    public function providerDGet(): array
    {
        return [
            [
                Functions::NAN(),
                $this->database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                10,
                $this->database1(),
                'Yield',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', '>12', null],
                ],
            ],
            [
                188000,
                $this->database2(),
                'Sales',
                [
                    ['Sales Rep.', 'Quarter'],
                    ['Tina', 4],
                ],
            ],
            [
                Functions::NAN(),
                $this->database2(),
                'Sales',
                [
                    ['Area', 'Quarter'],
                    ['South', 4],
                ],
            ],
            [
                null,
                $this->database1(),
                null,
                $this->database1(),
            ],
        ];
    }
}
