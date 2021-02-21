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

    protected function database()
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

    public function providerDAverage()
    {
        return [
            [
                12,
                $this->database(),
                'Yield',
                [
                    ['Tree', 'Height'],
                    ['=Apple', '>10'],
                ],
            ],
            [
                13,
                $this->database(),
                3,
                $this->database(),
            ],
        ];
    }
}
