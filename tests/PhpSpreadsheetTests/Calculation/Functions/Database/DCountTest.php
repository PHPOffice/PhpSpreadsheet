<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DCountTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDCount
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDCount($expectedResult, $database, $field, $criteria): void
    {
        $result = Database::DCOUNT($database, $field, $criteria);
        self::assertSame($expectedResult, $result);
    }

    private function database1(): array
    {
        return [
            ['Tree', 'Height', 'Age', 'Yield', 'Profit'],
            ['Apple', 18, 20, 14, 105],
            ['Pear', 12, 12, 10, 96],
            ['Cherry', 13, 14, 9, 105],
            ['Apple', 14, 'N/A', 10, 75],
            ['Pear', 9, 8, 8, 77],
            ['Apple', 12, 11, 6, 45],
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

    private function database3(): array
    {
        return [
            ['Status', 'Value'],
            [false, 1],
            [true, 2],
            [true, 4],
            [false, 8],
            [true, 16],
            [false, 32],
            [false, 64],
            [false, 128],
        ];
    }

    public function providerDCount(): array
    {
        return [
            [
                1,
                $this->database1(),
                'Age',
                [
                    ['Tree', 'Height', 'Height'],
                    ['=Apple', '>10', '<16'],
                ],
            ],
            [
                1,
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
                null,
                [
                    ['Subject', 'Score'],
                    ['English', '>63%'],
                ],
            ],
            [
                3,
                $this->database3(),
                'Value',
                [
                    ['Status'],
                    [true],
                ],
            ],
            [
                5,
                $this->database3(),
                'Value',
                [
                    ['Status'],
                    ['<>true'],
                ],
            ],
        ];
    }
}
