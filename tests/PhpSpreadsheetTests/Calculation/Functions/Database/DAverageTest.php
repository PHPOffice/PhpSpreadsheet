<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DAverageTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDAverage
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDAverage($expectedResult, $database, $field, $criteria): void
    {
        $result = Database::DAVERAGE($database, $field, $criteria);
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
            ['Pear', 9, 8, 8, 76.8],
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

    public function providerDAverage(): array
    {
        return [
            [
                12,
                $this->database1(),
                'Yield',
                [
                    ['Tree', 'Height'],
                    ['=Apple', '>10'],
                ],
            ],
            [
                13,
                $this->database1(),
                3,
                $this->database1(),
            ],
            [
                268333.333333333333,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Sales Rep.'],
                    ['>1', 'Tina'],
                ],
            ],
            [
                372500,
                $this->database2(),
                'Sales',
                [
                    ['Quarter', 'Area'],
                    ['1', 'South'],
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
