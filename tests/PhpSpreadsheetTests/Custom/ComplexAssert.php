<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Custom;

use Complex\Complex;
use PHPUnit\Framework\TestCase;

class ComplexAssert extends TestCase
{
    protected float $complexPrecision = 1E-12;

    private function adjustDelta(float $expected, float $actual, float $delta): float
    {
        $adjustedDelta = $delta;

        if (abs($actual) > 10 && abs($expected) > 10) {
            $variance = floor(log10(abs($expected)));
            $adjustedDelta *= 10 ** $variance;
        }

        return $adjustedDelta > 1.0 ? 1.0 : $adjustedDelta;
    }

    public function assertComplexEquals(mixed $expected, mixed $actual, ?float $delta = null): bool
    {
        if ($expected === INF) {
            self::assertSame('INF', $actual);

            return true;
        }
        if (is_string($expected) && $expected[0] === '#') {
            self::assertSame(
                $expected,
                $actual,
                'Mismatched Error'
            );

            return true;
        }

        if ($delta === null) {
            $delta = $this->complexPrecision;
        }
        $expectedComplex = new Complex($expected);
        $actualComplex = new Complex($actual);

        $comparand1 = $expectedComplex->getReal();
        $comparand2 = $actualComplex->getReal();
        $adjustedDelta = $this->adjustDelta($comparand1, $comparand2, $delta);
        self::assertEqualsWithDelta(
            $comparand1,
            $comparand2,
            $adjustedDelta,
            'Mismatched Real part'
        );

        $comparand1 = $expectedComplex->getImaginary();
        $comparand2 = $actualComplex->getImaginary();
        $adjustedDelta = $this->adjustDelta($comparand1, $comparand2, $delta);
        self::assertEqualsWithDelta(
            $comparand1,
            $comparand2,
            $adjustedDelta,
            'Mismatched Imaginary part'
        );

        self::assertSame(
            $expectedComplex->getSuffix(),
            $actualComplex->getSuffix(),
            'Mismatched Suffix'
        );

        return true;
    }
}
