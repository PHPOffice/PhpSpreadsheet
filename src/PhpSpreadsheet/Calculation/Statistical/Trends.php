<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Trend\Trend;

class Trends
{
    private static function filterTrendValues(array &$array1, array &$array2): void
    {
        foreach ($array1 as $key => $value) {
            if ((is_bool($value)) || (is_string($value)) || ($value === null)) {
                unset($array1[$key], $array2[$key]);
            }
        }
    }

    private static function checkTrendArrays(&$array1, &$array2)
    {
        if (!is_array($array1)) {
            $array1 = [$array1];
        }
        if (!is_array($array2)) {
            $array2 = [$array2];
        }

        $array1 = Functions::flattenArray($array1);
        $array2 = Functions::flattenArray($array2);

        self::filterTrendValues($array1, $array2);
        self::filterTrendValues($array2, $array1);

        // Reset the array indexes
        $array1 = array_merge($array1);
        $array2 = array_merge($array2);

        return true;
    }

    protected static function validateArrays(array $yValues, array $xValues): void
    {
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount === 0) || ($yValueCount !== $xValueCount)) {
            throw new Exception(Functions::NA());
        } elseif ($yValueCount === 1) {
            throw new Exception(Functions::DIV0());
        }
    }

    /**
     * CORREL.
     *
     * Returns covariance, the average of the products of deviations for each data point pair.
     *
     * @param mixed $yValues array of mixed Data Series Y
     * @param null|mixed $xValues array of mixed Data Series X
     *
     * @return float|string
     */
    public static function CORREL($yValues, $xValues = null)
    {
        if (($xValues === null) || (!is_array($yValues)) || (!is_array($xValues))) {
            return Functions::VALUE();
        }
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getCorrelation();
    }

    /**
     * COVAR.
     *
     * Returns covariance, the average of the products of deviations for each data point pair.
     *
     * @param mixed $yValues array of mixed Data Series Y
     * @param mixed $xValues array of mixed Data Series X
     *
     * @return float|string
     */
    public static function COVAR($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getCovariance();
    }

    /**
     * FORECAST.
     *
     * Calculates, or predicts, a future value by using existing values. The predicted value is a y-value for a given x-value.
     *
     * @param float $xValue Value of X for which we want to find Y
     * @param mixed $yValues array of mixed Data Series Y
     * @param mixed $xValues of mixed Data Series X
     *
     * @return bool|float|string
     */
    public static function FORECAST($xValue, $yValues, $xValues)
    {
        $xValue = Functions::flattenSingleValue($xValue);
        if (!is_numeric($xValue)) {
            return Functions::VALUE();
        } elseif (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getValueOfYForX($xValue);
    }

    /**
     * INTERCEPT.
     *
     * Calculates the point at which a line will intersect the y-axis by using existing x-values and y-values.
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string
     */
    public static function INTERCEPT($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getIntersect();
    }

    /**
     * LINEST.
     *
     * Calculates the statistics for a line by using the "least squares" method to calculate a straight line that best fits your data,
     *        and then returns an array that describes the line.
     *
     * @param mixed[] $yValues Data Series Y
     * @param null|mixed[] $xValues Data Series X
     * @param bool $const a logical value specifying whether to force the intersect to equal 0
     * @param bool $stats a logical value specifying whether to return additional regression statistics
     *
     * @return array|int|string The result, or a string containing an error
     */
    public static function LINEST($yValues, $xValues = null, $const = true, $stats = false)
    {
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);
        $stats = ($stats === null) ? false : (bool) Functions::flattenSingleValue($stats);
        if ($xValues === null) {
            $xValues = range(1, count(Functions::flattenArray($yValues)));
        }

        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues, $const);
        if ($stats) {
            return [
                [
                    $bestFitLinear->getSlope(),
                    $bestFitLinear->getSlopeSE(),
                    $bestFitLinear->getGoodnessOfFit(),
                    $bestFitLinear->getF(),
                    $bestFitLinear->getSSRegression(),
                ],
                [
                    $bestFitLinear->getIntersect(),
                    $bestFitLinear->getIntersectSE(),
                    $bestFitLinear->getStdevOfResiduals(),
                    $bestFitLinear->getDFResiduals(),
                    $bestFitLinear->getSSResiduals(),
                ],
            ];
        }

        return [
            $bestFitLinear->getSlope(),
            $bestFitLinear->getIntersect(),
        ];
    }

    /**
     * LOGEST.
     *
     * Calculates an exponential curve that best fits the X and Y data series,
     *        and then returns an array that describes the line.
     *
     * @param mixed[] $yValues Data Series Y
     * @param null|mixed[] $xValues Data Series X
     * @param bool $const a logical value specifying whether to force the intersect to equal 0
     * @param bool $stats a logical value specifying whether to return additional regression statistics
     *
     * @return array|int|string The result, or a string containing an error
     */
    public static function LOGEST($yValues, $xValues = null, $const = true, $stats = false)
    {
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);
        $stats = ($stats === null) ? false : (bool) Functions::flattenSingleValue($stats);
        if ($xValues === null) {
            $xValues = range(1, count(Functions::flattenArray($yValues)));
        }

        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        foreach ($yValues as $value) {
            if ($value <= 0.0) {
                return Functions::NAN();
            }
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitExponential = Trend::calculate(Trend::TREND_EXPONENTIAL, $yValues, $xValues, $const);
        if ($stats) {
            return [
                [
                    $bestFitExponential->getSlope(),
                    $bestFitExponential->getSlopeSE(),
                    $bestFitExponential->getGoodnessOfFit(),
                    $bestFitExponential->getF(),
                    $bestFitExponential->getSSRegression(),
                ],
                [
                    $bestFitExponential->getIntersect(),
                    $bestFitExponential->getIntersectSE(),
                    $bestFitExponential->getStdevOfResiduals(),
                    $bestFitExponential->getDFResiduals(),
                    $bestFitExponential->getSSResiduals(),
                ],
            ];
        }

        return [
            $bestFitExponential->getSlope(),
            $bestFitExponential->getIntersect(),
        ];
    }

    /**
     * RSQ.
     *
     * Returns the square of the Pearson product moment correlation coefficient through data points in known_y's and known_x's.
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string The result, or a string containing an error
     */
    public static function RSQ($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getGoodnessOfFit();
    }

    /**
     * SLOPE.
     *
     * Returns the slope of the linear regression line through data points in known_y's and known_x's.
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SLOPE($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getSlope();
    }

    /**
     * STEYX.
     *
     * Returns the standard error of the predicted y-value for each x in the regression.
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string
     */
    public static function STEYX($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }

        try {
            self::validateArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getStdevOfResiduals();
    }
}
