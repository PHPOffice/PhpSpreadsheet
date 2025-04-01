<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\Trend\Trend;

class Trends
{
    use ArrayEnabled;

    private static function filterTrendValues(array &$array1, array &$array2): void
    {
        foreach ($array1 as $key => $value) {
            if ((is_bool($value)) || (is_string($value)) || ($value === null)) {
                unset($array1[$key], $array2[$key]);
            }
        }
    }

    /**
     * @param mixed $array1 should be array, but scalar is made into one
     * @param mixed $array2 should be array, but scalar is made into one
     *
     * @param-out array $array1
     * @param-out array $array2
     */
    private static function checkTrendArrays(mixed &$array1, mixed &$array2): void
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
    }

    protected static function validateTrendArrays(array $yValues, array $xValues): void
    {
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount === 0) || ($yValueCount !== $xValueCount)) {
            throw new Exception(ExcelError::NA());
        } elseif ($yValueCount === 1) {
            throw new Exception(ExcelError::DIV0());
        }
    }

    /**
     * CORREL.
     *
     * Returns covariance, the average of the products of deviations for each data point pair.
     *
     * @param mixed $yValues array of mixed Data Series Y
     * @param null|mixed $xValues array of mixed Data Series X
     */
    public static function CORREL(mixed $yValues, $xValues = null): float|string
    {
        if (($xValues === null) || (!is_array($yValues)) || (!is_array($xValues))) {
            return ExcelError::VALUE();
        }

        try {
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
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
     * @param mixed[] $yValues array of mixed Data Series Y
     * @param mixed[] $xValues array of mixed Data Series X
     */
    public static function COVAR(array $yValues, array $xValues): float|string
    {
        try {
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getCovariance();
    }

    /**
     * FORECAST.
     *
     * Calculates, or predicts, a future value by using existing values.
     * The predicted value is a y-value for a given x-value.
     *
     * @param mixed $xValue Float value of X for which we want to find Y
     *                      Or can be an array of values
     * @param mixed[] $yValues array of mixed Data Series Y
     * @param mixed[] $xValues array of mixed Data Series X
     *
     * @return array|bool|float|string If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function FORECAST(mixed $xValue, array $yValues, array $xValues)
    {
        if (is_array($xValue)) {
            return self::evaluateArrayArgumentsSubset([self::class, __FUNCTION__], 1, $xValue, $yValues, $xValues);
        }

        try {
            $xValue = StatisticalValidations::validateFloat($xValue);
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getValueOfYForX($xValue);
    }

    /**
     * GROWTH.
     *
     * Returns values along a predicted exponential Trend
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     * @param mixed[] $newValues Values of X for which we want to find Y
     * @param mixed $const A logical (boolean) value specifying whether to force the intersect to equal 0 or not
     *
     * @return array<int, array<int, array<int, float>>>
     */
    public static function GROWTH(array $yValues, array $xValues = [], array $newValues = [], mixed $const = true): array
    {
        $yValues = Functions::flattenArray($yValues);
        $xValues = Functions::flattenArray($xValues);
        $newValues = Functions::flattenArray($newValues);
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);

        $bestFitExponential = Trend::calculate(Trend::TREND_EXPONENTIAL, $yValues, $xValues, $const);
        if (empty($newValues)) {
            $newValues = $bestFitExponential->getXValues();
        }

        $returnArray = [];
        foreach ($newValues as $xValue) {
            $returnArray[0][] = [$bestFitExponential->getValueOfYForX($xValue)];
        }

        return $returnArray;
    }

    /**
     * INTERCEPT.
     *
     * Calculates the point at which a line will intersect the y-axis by using existing x-values and y-values.
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     */
    public static function INTERCEPT(array $yValues, array $xValues): float|string
    {
        try {
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getIntersect();
    }

    /**
     * LINEST.
     *
     * Calculates the statistics for a line by using the "least squares" method to calculate a straight line
     *     that best fits your data, and then returns an array that describes the line.
     *
     * @param mixed[] $yValues Data Series Y
     * @param null|mixed[] $xValues Data Series X
     * @param mixed $const A logical (boolean) value specifying whether to force the intersect to equal 0 or not
     * @param mixed $stats A logical (boolean) value specifying whether to return additional regression statistics
     *
     * @return array|string The result, or a string containing an error
     */
    public static function LINEST(array $yValues, ?array $xValues = null, mixed $const = true, mixed $stats = false): string|array
    {
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);
        $stats = ($stats === null) ? false : (bool) Functions::flattenSingleValue($stats);
        if ($xValues === null) {
            $xValues = $yValues;
        }

        try {
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues, $const);

        if ($stats === true) {
            return [
                [
                    $bestFitLinear->getSlope(),
                    $bestFitLinear->getIntersect(),
                ],
                [
                    $bestFitLinear->getSlopeSE(),
                    ($const === false) ? ExcelError::NA() : $bestFitLinear->getIntersectSE(),
                ],
                [
                    $bestFitLinear->getGoodnessOfFit(),
                    $bestFitLinear->getStdevOfResiduals(),
                ],
                [
                    $bestFitLinear->getF(),
                    $bestFitLinear->getDFResiduals(),
                ],
                [
                    $bestFitLinear->getSSRegression(),
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
     * @param mixed $const A logical (boolean) value specifying whether to force the intersect to equal 0 or not
     * @param mixed $stats A logical (boolean) value specifying whether to return additional regression statistics
     *
     * @return array|string The result, or a string containing an error
     */
    public static function LOGEST(array $yValues, ?array $xValues = null, mixed $const = true, mixed $stats = false): string|array
    {
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);
        $stats = ($stats === null) ? false : (bool) Functions::flattenSingleValue($stats);
        if ($xValues === null) {
            $xValues = $yValues;
        }

        try {
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        foreach ($yValues as $value) {
            if ($value < 0.0) {
                return ExcelError::NAN();
            }
        }

        $bestFitExponential = Trend::calculate(Trend::TREND_EXPONENTIAL, $yValues, $xValues, $const);

        if ($stats === true) {
            return [
                [
                    $bestFitExponential->getSlope(),
                    $bestFitExponential->getIntersect(),
                ],
                [
                    $bestFitExponential->getSlopeSE(),
                    ($const === false) ? ExcelError::NA() : $bestFitExponential->getIntersectSE(),
                ],
                [
                    $bestFitExponential->getGoodnessOfFit(),
                    $bestFitExponential->getStdevOfResiduals(),
                ],
                [
                    $bestFitExponential->getF(),
                    $bestFitExponential->getDFResiduals(),
                ],
                [
                    $bestFitExponential->getSSRegression(),
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
     * Returns the square of the Pearson product moment correlation coefficient through data points
     *     in known_y's and known_x's.
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     *
     * @return float|string The result, or a string containing an error
     */
    public static function RSQ(array $yValues, array $xValues)
    {
        try {
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
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
    public static function SLOPE(array $yValues, array $xValues)
    {
        try {
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
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
     */
    public static function STEYX(array $yValues, array $xValues): float|string
    {
        try {
            self::checkTrendArrays($yValues, $xValues);
            self::validateTrendArrays($yValues, $xValues);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getStdevOfResiduals();
    }

    /**
     * TREND.
     *
     * Returns values along a linear Trend
     *
     * @param mixed[] $yValues Data Series Y
     * @param mixed[] $xValues Data Series X
     * @param mixed[] $newValues Values of X for which we want to find Y
     * @param mixed $const A logical (boolean) value specifying whether to force the intersect to equal 0 or not
     *
     * @return array<int, array<int, array<int, float>>>
     */
    public static function TREND(array $yValues, array $xValues = [], array $newValues = [], mixed $const = true): array
    {
        $yValues = Functions::flattenArray($yValues);
        $xValues = Functions::flattenArray($xValues);
        $newValues = Functions::flattenArray($newValues);
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues, $const);
        if (empty($newValues)) {
            $newValues = $bestFitLinear->getXValues();
        }

        $returnArray = [];
        foreach ($newValues as $xValue) {
            $returnArray[0][] = [$bestFitLinear->getValueOfYForX($xValue)];
        }

        return $returnArray;
    }
}
