<?php

namespace PhpOffice\PhpSpreadsheetTests\Custom;

use Complex\Complex;
use PHPUnit\Framework\TestCase;

class ComplexAssert extends TestCase
{
    /**
     * @var string
     */
    private $errorMessage = '';

    /** @var float */
    private $delta = 0.0;

    public function __construct()
    {
        parent::__construct('complexAssert');
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    private function testExpectedExceptions($expected, $actual): bool
    {
        //    Expecting an error, so we do a straight string comparison
        if ($expected === $actual) {
            return true;
        } elseif ($expected === INF && $actual === 'INF') {
            return true;
        }
        $this->errorMessage = 'Expected Error: ' . $actual . ' !== ' . $expected;

        return false;
    }

    private function adjustDelta(float $expected, float $actual, float $delta): float
    {
        $adjustedDelta = $delta;

        if (abs($actual) > 10 && abs($expected) > 10) {
            $variance = floor(log10(abs($expected)));
            $adjustedDelta *= 10 ** $variance;
        }

        return $adjustedDelta > 1.0 ? 1.0 : $adjustedDelta;
    }

    public function setDelta(float $delta): self
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function assertComplexEquals($expected, $actual, ?float $delta = null): bool
    {
        if ($expected === INF || (is_string($expected) && $expected[0] === '#')) {
            return $this->testExpectedExceptions($expected, $actual);
        }

        if ($delta === null) {
            $delta = $this->delta;
        }
        $expectedComplex = new Complex($expected);
        $actualComplex = new Complex($actual);

        $adjustedDelta = $this->adjustDelta($expectedComplex->getReal(), $actualComplex->getReal(), $delta);
        if (abs($actualComplex->getReal() - $expectedComplex->getReal()) > $adjustedDelta) {
            $this->errorMessage = 'Mismatched Real part: ' . $actualComplex->getReal() . ' != ' . $expectedComplex->getReal();

            return false;
        }

        $adjustedDelta = $this->adjustDelta($expectedComplex->getImaginary(), $actualComplex->getImaginary(), $delta);
        if (abs($actualComplex->getImaginary() - $expectedComplex->getImaginary()) > $adjustedDelta) {
            $this->errorMessage = 'Mismatched Imaginary part: ' . $actualComplex->getImaginary() . ' != ' . $expectedComplex->getImaginary();

            return false;
        }

        if ($actualComplex->getSuffix() !== $actualComplex->getSuffix()) {
            $this->errorMessage = 'Mismatched Suffix: ' . $actualComplex->getSuffix() . ' != ' . $expectedComplex->getSuffix();

            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function runAssertComplexEquals($expected, $actual, ?float $delta = null): void
    {
        self::assertTrue($this->assertComplexEquals($expected, $actual, $delta), $this->getErrorMessage());
    }
}
