<?php

namespace PhpOffice\PhpSpreadsheetTests\Custom;

class ComplexAssert
{
    private $errorMessage = '';

    public function assertComplexEquals($expected, $actual, $delta = 0)
    {
        if ($expected[0] === '#') {
            //    Expecting an error, so we do a straight string comparison
            if ($expected === $actual) {
                return true;
            }
            $this->errorMessage = 'Expected Error: ' . $actual . ' !== ' . $expected;

            return false;
        }

        $expectedComplex = new Complex($expected);
        $actualComplex = new Complex($actual);

        if (!is_numeric($actualComplex->getReal()) || !is_numeric($expectedComplex->getReal())) {
            if ($actualComplex->getReal() !== $expectedComplex->getReal()) {
                $this->errorMessage = 'Mismatched String: ' . $actualComplex->getReal() . ' !== ' . $expectedComplex->getReal();

                return false;
            }

            return true;
        }

        if ($actualComplex->getReal() < ($expectedComplex->getReal() - $delta) ||
            $actualComplex->getReal() > ($expectedComplex->getReal() + $delta)) {
            $this->errorMessage = 'Mismatched Real part: ' . $actualComplex->getReal() . ' != ' . $expectedComplex->getReal();

            return false;
        }

        if ($actualComplex->getImaginary() < ($expectedComplex->getImaginary() - $delta) ||
            $actualComplex->getImaginary() > ($expectedComplex->getImaginary() + $delta)) {
            $this->errorMessage = 'Mismatched Imaginary part: ' . $actualComplex->getImaginary() . ' != ' . $expectedComplex->getImaginary();

            return false;
        }

        if ($actualComplex->getSuffix() !== $actualComplex->getSuffix()) {
            $this->errorMessage = 'Mismatched Suffix: ' . $actualComplex->getSuffix() . ' != ' . $expectedComplex->getSuffix();

            return false;
        }

        return true;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
