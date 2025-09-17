<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ConvertSpecialArrayTest extends TestCase
{
    /**
     * @param mixed[] $expected
     * @param mixed[] $inArray
     */
    #[DataProvider('providerSpecialArrays')]
    public function testConvertSpecialArray(array $expected, array $inArray): void
    {
        $reflectionMethod = new ReflectionMethod(Cell::class, 'convertSpecialArray');
        $result = $reflectionMethod->invokeArgs(null, [$inArray]);
        self::assertSame($expected, $result);
    }

    public static function providerSpecialArrays(): array
    {
        return [
            'expected form row index to array indexed by column' => [
                [
                    [1, 2],
                    [3, 4],
                ],
                [
                    1 => ['A' => 1, 'B' => 2],
                    2 => ['A' => 3, 'B' => 4],
                ],
            ],
            'standard array unchanged' => [
                [
                    1 => [1, 2],
                    2 => [3, 4],
                ],
                [
                    1 => [1, 2],
                    2 => [3, 4],
                ],
            ],
            'uses index 0 so unchanged' => [
                [
                    ['A' => 1, 'B' => 2],
                    ['A' => 3, 'B' => 4],
                ],
                [
                    ['A' => 1, 'B' => 2],
                    ['A' => 3, 'B' => 4],
                ],
            ],
        ];
    }
}
