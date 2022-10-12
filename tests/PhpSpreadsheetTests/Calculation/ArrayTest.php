<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase
{
    public function testMultiDimensionalArrayIsFlattened(): void
    {
        $array = [
            0 => [
                0 => [
                    32 => [
                        'B' => 'PHP',
                    ],
                ],
            ],
            1 => [
                0 => [
                    32 => [
                        'C' => 'Spreadsheet',
                    ],
                ],
            ],
        ];

        $values = Functions::flattenArray($array);

        self::assertIsNotArray($values[0]);
        self::assertIsNotArray($values[1]);
    }
}
