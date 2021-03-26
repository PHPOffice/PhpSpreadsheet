<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use Complex\Complex as ComplexObject;
use Complex\Exception as ComplexException;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Complex
{
    use BaseValidations;

    /**
     * COMPLEX.
     *
     * Converts real and imaginary coefficients into a complex number of the form x +/- yi or x +/- yj.
     *
     * Excel Function:
     *        COMPLEX(realNumber,imaginary[,suffix])
     *
     * @param mixed (float) $realNumber the real coefficient of the complex number
     * @param mixed (float) $imaginary the imaginary coefficient of the complex number
     * @param mixed (string) $suffix The suffix for the imaginary component of the complex number.
     *                                        If omitted, the suffix is assumed to be "i".
     *
     * @return string
     */
    public static function COMPLEX($realNumber = 0.0, $imaginary = 0.0, $suffix = 'i')
    {
        $realNumber = ($realNumber === null) ? 0.0 : Functions::flattenSingleValue($realNumber);
        $imaginary = ($imaginary === null) ? 0.0 : Functions::flattenSingleValue($imaginary);
        $suffix = ($suffix === null) ? 'i' : Functions::flattenSingleValue($suffix);

        try {
            $realNumber = self::validateFloat($realNumber);
            $imaginary = self::validateFloat($imaginary);
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
     * @param string $complexNumber the complex number for which you want the imaginary
     *                                         coefficient
     *
     * @return float|string
     */
    public static function IMAGINARY($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

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
     * @param string $complexNumber the complex number for which you want the real coefficient
     *
     * @return float|string
     */
    public static function IMREAL($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        try {
            $complex = new ComplexObject($complexNumber);
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return $complex->getReal();
    }
}
