<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use Complex\Complex as ComplexObject;
use Complex\Exception as ComplexException;
use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class ComplexOperations
{
    use ArrayEnabled;

    /**
     * IMDIV.
     *
     * Returns the quotient of two complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMDIV(complexDividend,complexDivisor)
     *
     * @param array|string $complexDividend the complex numerator or dividend
     *                      Or can be an array of values
     * @param array|string $complexDivisor the complex denominator or divisor
     *                      Or can be an array of values
     *
     * @return array|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function IMDIV($complexDividend, $complexDivisor)
    {
        if (is_array($complexDividend) || is_array($complexDivisor)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $complexDividend, $complexDivisor);
        }

        try {
            return (string) (new ComplexObject($complexDividend))->divideby(new ComplexObject($complexDivisor));
        } catch (ComplexException $e) {
            return ExcelError::NAN();
        }
    }

    /**
     * IMSUB.
     *
     * Returns the difference of two complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSUB(complexNumber1,complexNumber2)
     *
     * @param array|string $complexNumber1 the complex number from which to subtract complexNumber2
     *                      Or can be an array of values
     * @param array|string $complexNumber2 the complex number to subtract from complexNumber1
     *                      Or can be an array of values
     *
     * @return array|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function IMSUB($complexNumber1, $complexNumber2)
    {
        if (is_array($complexNumber1) || is_array($complexNumber2)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $complexNumber1, $complexNumber2);
        }

        try {
            return (string) (new ComplexObject($complexNumber1))->subtract(new ComplexObject($complexNumber2));
        } catch (ComplexException $e) {
            return ExcelError::NAN();
        }
    }

    /**
     * IMSUM.
     *
     * Returns the sum of two or more complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSUM(complexNumber[,complexNumber[,...]])
     *
     * @param string ...$complexNumbers Series of complex numbers to add
     *
     * @return string
     */
    public static function IMSUM(...$complexNumbers)
    {
        // Return value
        $returnValue = new ComplexObject(0.0);
        $aArgs = Functions::flattenArray($complexNumbers);

        try {
            // Loop through the arguments
            foreach ($aArgs as $complex) {
                $returnValue = $returnValue->add(new ComplexObject($complex));
            }
        } catch (ComplexException $e) {
            return ExcelError::NAN();
        }

        return (string) $returnValue;
    }

    /**
     * IMPRODUCT.
     *
     * Returns the product of two or more complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMPRODUCT(complexNumber[,complexNumber[,...]])
     *
     * @param string ...$complexNumbers Series of complex numbers to multiply
     *
     * @return string
     */
    public static function IMPRODUCT(...$complexNumbers)
    {
        // Return value
        $returnValue = new ComplexObject(1.0);
        $aArgs = Functions::flattenArray($complexNumbers);

        try {
            // Loop through the arguments
            foreach ($aArgs as $complex) {
                $returnValue = $returnValue->multiply(new ComplexObject($complex));
            }
        } catch (ComplexException $e) {
            return ExcelError::NAN();
        }

        return (string) $returnValue;
    }
}
