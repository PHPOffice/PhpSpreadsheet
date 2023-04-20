<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class UniqueTest extends TestCase
{
    /**
     * @dataProvider uniqueTestProvider
     */
    public function testUnique(array $expectedResult, array $lookupRef, bool $byColumn = false, bool $exactlyOnce = false): void
    {
        $result = LookupRef\Unique::unique($lookupRef, $byColumn, $exactlyOnce);
        self::assertEquals($expectedResult, $result);
    }

    public function testUniqueException(): void
    {
        $rowLookupData = [
            ['Andrew', 'Brown'],
            ['Betty', 'Johnson'],
            ['Betty', 'Johnson'],
            ['Andrew', 'Brown'],
            ['David', 'White'],
            ['Andrew', 'Brown'],
            ['David', 'White'],
        ];

        $columnLookupData = [
            ['PHP', 'Rocks', 'php', 'rocks'],
        ];

        $result = LookupRef\Unique::unique($rowLookupData, false, true);
        self::assertEquals(ExcelError::CALC(), $result);

        $result = LookupRef\Unique::unique($columnLookupData, true, true);
        self::assertEquals(ExcelError::CALC(), $result);
    }

    public function testUniqueWithScalar(): void
    {
        $lookupData = 123;

        $result = LookupRef\Unique::unique($lookupData);
        self::assertSame($lookupData, $result);
    }

    public static function uniqueTestProvider(): array
    {
        return [
            [
                [['Red'], ['Green'], ['Blue'], ['Orange']],
                [
                    ['Red'],
                    ['Green'],
                    ['Green'],
                    ['Blue'],
                    ['Blue'],
                    ['Orange'],
                    ['Green'],
                    ['Blue'],
                    ['Red'],
                ],
            ],
            [
                [['Red'], ['Green'], ['Blue'], ['Orange']],
                [
                    ['Red'],
                    ['Green'],
                    ['GrEEn'],
                    ['Blue'],
                    ['BLUE'],
                    ['Orange'],
                    ['GReeN'],
                    ['blue'],
                    ['RED'],
                ],
            ],
            [
                ['Orange'],
                [
                    ['Red'],
                    ['Green'],
                    ['Green'],
                    ['Blue'],
                    ['Blue'],
                    ['Orange'],
                    ['Green'],
                    ['Blue'],
                    ['Red'],
                ],
                false,
                true,
            ],
            [
                ['Andrew', 'Betty', 'Robert', 'David'],
                [['Andrew', 'Betty', 'Robert', 'Andrew', 'Betty', 'Robert', 'David', 'Andrew']],
                true,
            ],
            [
                ['David'],
                [['Andrew', 'Betty', 'Robert', 'Andrew', 'Betty', 'Robert', 'David', 'Andrew']],
                true,
                true,
            ],
            [
                [1, 1, 2, 2, 3],
                [[1, 1, 2, 2, 3]],
            ],
            [
                [1, 2, 3],
                [[1, 1, 2, 2, 3]],
                true,
            ],
            [
                [
                    [1, 1, 2, 3],
                    [1, 2, 2, 3],
                ],
                [
                    [1, 1, 2, 2, 3],
                    [1, 2, 2, 2, 3],
                ],
                true,
            ],
            [
                [
                    ['Andrew', 'Brown'],
                    ['Betty', 'Johnson'],
                    ['David', 'White'],
                ],
                [
                    ['Andrew', 'Brown'],
                    ['Betty', 'Johnson'],
                    ['Betty', 'Johnson'],
                    ['Andrew', 'Brown'],
                    ['David', 'White'],
                    ['Andrew', 'Brown'],
                    ['David', 'White'],
                ],
            ],
            [
                [[1.2], [2.1], [2.2], [3.0]],
                [
                    [1.2],
                    [1.2],
                    [2.1],
                    [2.2],
                    [3.0],
                ],
            ],
        ];
    }
}
