<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DCountATest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDCountA
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDCountA($expectedResult, $database, $field, $criteria): void
    {
        $result = Database::DCOUNTA($database, $field, $criteria);
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
            ['Name', 'Gender', 'Age', 'Subject', 'Score'],
            ['Amy', 'Female', 8, 'Math', 0.63],
            ['Amy', 'Female', 8, 'English', 0.78],
            ['Amy', 'Female', 8, 'Science', 0.39],
            ['Bill', 'Male', 8, 'Math', 0.55],
            ['Bill', 'Male', 8, 'English', 0.71],
            ['Bill', 'Male', 8, 'Science', 'awaiting'],
            ['Sue', 'Female', 9, 'Math', null],
            ['Sue', 'Female', 9, 'English', 0.52],
            ['Sue', 'Female', 9, 'Science', 0.48],
            ['Tom', 'Male', 9, 'Math', 0.78],
            ['Tom', 'Male', 9, 'English', 0.69],
            ['Tom', 'Male', 9, 'Science', 0.65],
        ];
    }

    public function providerDCountA(): array
    {
        return [
            [
                1,
                $this->database1(),
                'Profit',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                ],
            ],
            [
                2,
                $this->database2(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Science', 'Male'],
                ],
            ],
            [
                1,
                $this->database2(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Female'],
                ],
            ],
            [
                3,
                $this->database2(),
                'Score',
                [
                    ['Subject', 'Score'],
                    ['English', '>60%'],
                ],
            ],
        ];
    }
}
