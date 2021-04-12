<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DMinTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDMin
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDMin($expectedResult, $database, $field, $criteria): void
    {
        $result = Database::DMIN($database, $field, $criteria);
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
            ['Name', 'Gender', 'Age', 'Subject', 'Score'],
            ['Amy', 'Female', 8, 'Math', 0.63],
            ['Amy', 'Female', 8, 'English', 0.78],
            ['Amy', 'Female', 8, 'Science', 0.39],
            ['Bill', 'Male', 8, 'Math', 0.55],
            ['Bill', 'Male', 8, 'English', 0.71],
            ['Bill', 'Male', 8, 'Science', 0.51],
            ['Sue', 'Female', 9, 'Math', 0.39],
            ['Sue', 'Female', 9, 'English', 0.52],
            ['Sue', 'Female', 9, 'Science', 0.48],
            ['Tom', 'Male', 9, 'Math', 0.78],
            ['Tom', 'Male', 9, 'English', 0.69],
            ['Tom', 'Male', 9, 'Science', 0.65],
        ];
    }

    public function providerDMin(): array
    {
        return [
            [
                75,
                $this->database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                    ['=Pear', '>12', null],
                ],
            ],
            [
                0.48,
                $this->database2(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Science', '>8'],
                ],
            ],
            [
                0.55,
                $this->database2(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Male'],
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
