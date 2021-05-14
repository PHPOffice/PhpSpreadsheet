<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Angle
{
    /**
     * DEGREES.
     *
     * Returns the result of builtin function rad2deg after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function toDegrees($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return rad2deg($number);
    }

    /**
     * RADIANS.
     *
     * Returns the result of builtin function deg2rad after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function toRadians($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return deg2rad($number);
    }
}
