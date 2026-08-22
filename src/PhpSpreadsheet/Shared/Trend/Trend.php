<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Trend;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;

class Trend
{
    const TREND_LINEAR = 'Linear';
    const TREND_LOGARITHMIC = 'Logarithmic';
    const TREND_EXPONENTIAL = 'Exponential';
    const TREND_POWER = 'Power';
    const TREND_POLYNOMIAL_2 = 'Polynomial_2';
    const TREND_POLYNOMIAL_3 = 'Polynomial_3';
    const TREND_POLYNOMIAL_4 = 'Polynomial_4';
    const TREND_POLYNOMIAL_5 = 'Polynomial_5';
    const TREND_POLYNOMIAL_6 = 'Polynomial_6';
    const TREND_BEST_FIT = 'Bestfit';
    const TREND_BEST_FIT_NO_POLY = 'Bestfit_no_Polynomials';

    /**
     * Names of the best-fit Trend analysis methods.
     */
    private const TREND_TYPES = [
        self::TREND_LINEAR,
        self::TREND_LOGARITHMIC,
        self::TREND_EXPONENTIAL,
        self::TREND_POWER,
    ];

    /**
     * Names of the best-fit Trend polynomial orders.
     *
     * @var string[]
     */
    private static array $trendTypePolynomialOrders = [
        self::TREND_POLYNOMIAL_2,
        self::TREND_POLYNOMIAL_3,
        self::TREND_POLYNOMIAL_4,
        self::TREND_POLYNOMIAL_5,
        self::TREND_POLYNOMIAL_6,
    ];

    /**
     * Cached results for each method when trying to identify which provides the best fit.
     *
     * @var BestFit[]
     */
    private static array $trendCache = [];

    /**
     * @param mixed[] $yValues
     * @param mixed[] $xValues
     */
    public static function calculate(string $trendType = self::TREND_BEST_FIT, array $yValues = [], array $xValues = [], bool $const = true): BestFit
    {
        //    Calculate number of points in each dataset
        /** @var float[] $xValues */
        $nY = count($yValues);
        /** @var float[] $xValues */
        $nX = count($xValues);

        //    Define X Values if necessary
        if ($nX === 0) {
            $xValues = range(1, $nY);
        } elseif ($nY !== $nX) {
            //    Ensure both arrays of points are the same size
            throw new SpreadsheetException('Trend(): Number of elements in coordinate arrays do not match.');
        }

        $key = md5($trendType . $const . serialize($yValues) . serialize($xValues));
        //    Determine which Trend method has been requested
        switch ($trendType) {
            //    Instantiate and return the class for the requested Trend method
            case self::TREND_LINEAR:
            case self::TREND_LOGARITHMIC:
            case self::TREND_EXPONENTIAL:
            case self::TREND_POWER:
                if (!isset(self::$trendCache[$key])) {
                    /** @var float[] $yValues */
                    $className = '\PhpOffice\PhpSpreadsheet\Shared\Trend\\' . $trendType . 'BestFit';
                    /** @var float[] $xValues */
                    self::$trendCache[$key] = new $className($yValues, $xValues, $const);
                }

                return self::$trendCache[$key];
            case self::TREND_POLYNOMIAL_2:
            case self::TREND_POLYNOMIAL_3:
            case self::TREND_POLYNOMIAL_4:
            case self::TREND_POLYNOMIAL_5:
            case self::TREND_POLYNOMIAL_6:
                if (!isset(self::$trendCache[$key])) {
                    $order = (int) substr($trendType, -1);
                    /** @var float[] $yValues */
                    self::$trendCache[$key] = new PolynomialBestFit($order, $yValues, $xValues);
                }

                return self::$trendCache[$key];
            case self::TREND_BEST_FIT:
            case self::TREND_BEST_FIT_NO_POLY:
                //    If the request is to determine the best fit regression, then we test each Trend line in turn
                //    Start by generating an instance of each available Trend method
                /** @var float[] $yValues */
                $bestFit = [];
                /** @var float[] $xValues */
                $bestFitValue = [];
                foreach (self::TREND_TYPES as $trendMethod) {
                    $className = '\PhpOffice\PhpSpreadsheet\Shared\Trend\\' . $trendMethod . 'BestFit';
                    $bestFit[$trendMethod] = new $className($yValues, $xValues, $const);
                    $bestFitValue[$trendMethod] = $bestFit[$trendMethod]->getGoodnessOfFit();
                }
                if ($trendType !== self::TREND_BEST_FIT_NO_POLY) {
                    foreach (self::$trendTypePolynomialOrders as $trendMethod) {
                        $order = (int) substr($trendMethod, -1);
                        $bestFit[$trendMethod] = new PolynomialBestFit($order, $yValues, $xValues);
                        if ($bestFit[$trendMethod]->getError()) {
                            unset($bestFit[$trendMethod]);
                        } else {
                            $bestFitValue[$trendMethod] = $bestFit[$trendMethod]->getGoodnessOfFit();
                        }
                    }
                }
                //    Determine which of our Trend lines is the best fit, and then we return the instance of that Trend class
                arsort($bestFitValue);
                $bestFitType = key($bestFitValue);

                return $bestFit[$bestFitType];
            default:
                throw new SpreadsheetException("Unknown trend type $trendType");
        }
    }
}
