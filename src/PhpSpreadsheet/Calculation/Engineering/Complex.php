<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use Complex\Complex as ComplexObject;
use Complex\Exception as ComplexException;
use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Complex
{
    use ArrayEnabled;

    /**
     * COMPLEX.
     *
     * Converts real and imaginary coefficients into a complex number of the form x +/- yi or x +/- yj.
     *
     * Excel Function:
     *        COMPLEX(realNumber,imaginary[,suffix])
     *
     * @param mixed $realNumber the real float coefficient of the complex number
     *                      Or can be an array of values
     * @param mixed $imaginary the imaginary float coefficient of the complex number
     *                      Or can be an array of values
     * @param mixed $suffix The character suffix for the imaginary component of the complex number.
     *                          If omitted, the suffix is assumed to be "i".
     *                      Or can be an array of values
     *
     * @return array|string
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function COMPLEX($realNumber = 0.0, $imaginary = 0.0, $suffix = 'i')
    {
        if (is_array($realNumber) || is_array($imaginary) || is_array($suffix)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $realNumber, $imaginary, $suffix);
        }

        $realNumber = $realNumber ?? 0.0;
        $imaginary = $imaginary ?? 0.0;
        $suffix = $suffix ?? 'i';

        try {
            $realNumber = EngineeringValidations::validateFloat($realNumber);
            $imaginary = EngineeringValidations::validateFloat($imaginary);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($suffix == 'i') || ($suffix == 'j') || ($suffix == '')) {
            $complex = new ComplexObject($realNumber, $imaginary, $suffix);

            return (string) $complex;
        }

        return Functions::VALUE();
    }

    /**
     * IMAGINARY.
     *
     * Returns the imaginary coefficient of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMAGINARY(complexNumber)
     *
     * @param array|string $complexNumber the complex number for which you want the imaginary
     *                                         coefficient
     *                      Or can be an array of values
     *
     * @return array|float|string (string if an error)
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function IMAGINARY($complexNumber)
    {
        if (is_array($complexNumber)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $complexNumber);
        }

        try {
            $complex = new ComplexObject($complexNumber);
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return $complex->getImaginary();
    }

    /**
     * IMREAL.
     *
     * Returns the real coefficient of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMREAL(complexNumber)
     *
     * @param array|string $complexNumber the complex number for which you want the real coefficient
     *                      Or can be an array of values
     *
     * @return array|float|string (string if an error)
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function IMREAL($complexNumber)
    {
        if (is_array($complexNumber)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $complexNumber);
        }

        try {
            $complex = new ComplexObject($complexNumber);
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return $complex->getReal();
    }
}
