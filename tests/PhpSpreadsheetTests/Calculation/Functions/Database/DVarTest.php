<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DVarTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDVar
     *
     * @param mixed $expectedResult
     * @param mixed $database
     * @param mixed $field
     * @param mixed $criteria
     */
    public function testDVar($expectedResult, $database, $field, $criteria): void
    {
        $result = Database::DVAR($database, $field, $criteria);
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
            ['Amy', 'Female', 10, 'Math', 0.63],
            ['Amy', 'Female', 10, 'English', 0.78],
            ['Amy', 'Female', 10, 'Science', 0.39],
            ['Bill', 'Male', 8, 'Math', 0.55],
            ['Bill', 'Male', 8, 'English', 0.71],
            ['Bill', 'Male', 8, 'Science', 0.51],
            ['Sam', 'Male', 9, 'Math', 0.39],
            ['Sam', 'Male', 9, 'English', 0.52],
            ['Sam', 'Male', 9, 'Science', 0.48],
            ['Tom', 'Male', 9, 'Math', 0.78],
            ['Tom', 'Male', 9, 'English', 0.69],
            ['Tom', 'Male', 9, 'Science', 0.65],
        ];
    }

    public function providerDVar(): array
    {
        return [
            [
                8.8,
                $this->database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.038433333333,
                $this->database2(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Male'],
                ],
            ],
            [
                0.017433333333,
                $this->database2(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Science', '>8'],
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
