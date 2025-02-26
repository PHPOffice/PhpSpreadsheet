<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class RandArrayTest extends AllSetupTeardown
{
    public function testRANDARRAYInteger(): void
    {
        $rows = 3;
        $cols = 2;
        $min = -5;
        $max = 5;

        $result = MathTrig\Random::randArray($rows, $cols, $min, $max, true);
        self::assertIsArray($result);
        self::assertCount($rows, $result);
        self::assertIsArray($result[0]);
        self::assertCount($cols, $result[0]);

        $values = Functions::flattenArray($result);
        array_walk(
            $values,
            function (mixed $value) use ($min, $max): void {
                self::assertIsInt($value);
                self::assertTrue($value >= $min && $value <= $max);
            }
        );
    }

    public function testRANDARRAYFloat(): void
    {
        $rows = 3;
        $cols = 2;
        $min = -2;
        $max = 2;

        $result = MathTrig\Random::randArray($rows, $cols, $min, $max, false);
        self::assertIsArray($result);
        self::assertCount($rows, $result);
        self::assertIsArray($result[0]);
        self::assertCount($cols, $result[0]);

        $values = Functions::flattenArray($result);
        array_walk(
            $values,
            function (mixed $value) use ($min, $max): void {
                self::assertIsFloat($value);
                self::assertTrue($value >= $min && $value <= $max);
            }
        );
    }

    public function testRANDARRAYExceptions(): void
    {
        $rows = 'THREE';
        $cols = 2;
        $min = 2;
        $max = -2;

        $result = MathTrig\Random::randArray($rows, $cols, $min, $max, false);
        self::assertSame(ExcelError::VALUE(), $result);

        $rows = 3;
        $cols = 2;
        $min = 2;
        $max = -2;

        $result = MathTrig\Random::randArray($rows, $cols, $min, $max, false);
        self::assertSame(ExcelError::VALUE(), $result);
    }
}
