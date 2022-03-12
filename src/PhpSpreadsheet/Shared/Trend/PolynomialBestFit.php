<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Trend;

use PhpOffice\PhpSpreadsheet\Shared\JAMA\Matrix;

class PolynomialBestFit extends BestFit
{
    /**
     * Algorithm type to use for best-fit
     * (Name of this Trend class).
     *
     * @var string
     */
    protected $bestFitType = 'polynomial';

    /**
     * Polynomial order.
     *
     * @var int
     */
    protected $order = 0;

    /**
     * Return the order of this polynomial.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Return the Y-Value for a specified value of X.
     *
     * @param float $xValue X-Value
     *
     * @return float Y-Value
     */
    public function getValueOfYForX($xValue)
    {
        $retVal = $this->getIntersect();
        $slope = $this->getSlope();
        // @phpstan-ignore-next-line
        foreach ($slope as $key => $value) {
            if ($value != 0.0) {
                $retVal += $value * $xValue ** ($key + 1);
            }
        }

        return $retVal;
    }

    /**
     * Return the X-Value for a specified value of Y.
     *
     * @param float $yValue Y-Value
     *
     * @return float X-Value
     */
    public function getValueOfXForY($yValue)
    {
        return ($yValue - $this->getIntersect()) / $this->getSlope();
    }

    /**
     * Return the Equation of the best-fit line.
     *
     * @param int $dp Number of places of decimal precision to display
     *
     * @return string
     */
    public function getEquation($dp = 0)
    {
        $slope = $this->getSlope($dp);
        $intersect = $this->getIntersect($dp);

        $equation = 'Y = ' . $intersect;
        // @phpstan-ignore-next-line
        foreach ($slope as $key => $value) {
            if ($value != 0.0) {
                $equation .= ' + ' . $value . ' * X';
                if ($key > 0) {
                    $equation .= '^' . ($key + 1);
                }
            }
        }

        return $equation;
    }

    /**
     * Return the Slope of the line.
     *
     * @param int $dp Number of places of decimal precision to display
     *
     * @return float
     */
    public function getSlope($dp = 0)
    {
        if ($dp != 0) {
            $coefficients = [];
            foreach ($this->slope as $coefficient) {
                $coefficients[] = round($coefficient, $dp);
            }

            // @phpstan-ignore-next-line
            return $coefficients;
        }

        return $this->slope;
    }

    public function getCoefficients($dp = 0)
    {
        // @phpstan-ignore-next-line
        return array_merge([$this->getIntersect($dp)], $this->getSlope($dp));
    }

    /**
     * Execute the regression and calculate the goodness of fit for a set of X and Y data values.
     *
     * @param int $order Order of Polynomial for this regression
     * @param float[] $yValues The set of Y-values for this regression
     * @param float[] $xValues The set of X-values for this regression
     */
    private function polynomialRegression($order, $yValues, $xValues): void
    {
        // calculate sums
        $x_sum = array_sum($xValues);
        $y_sum = array_sum($yValues);
        $xx_sum = $xy_sum = $yy_sum = 0;
        for ($i = 0; $i < $this->valueCount; ++$i) {
            $xy_sum += $xValues[$i] * $yValues[$i];
            $xx_sum += $xValues[$i] * $xValues[$i];
            $yy_sum += $yValues[$i] * $yValues[$i];
        }
        /*
         *    This routine uses logic from the PHP port of polyfit version 0.1
         *    written by Michael Bommarito and Paul Meagher
         *
         *    The function fits a polynomial function of order $order through
         *    a series of x-y data points using least squares.
         *
         */
        $A = [];
        $B = [];
        for ($i = 0; $i < $this->valueCount; ++$i) {
            for ($j = 0; $j <= $order; ++$j) {
                $A[$i][$j] = $xValues[$i] ** $j;
            }
        }
        for ($i = 0; $i < $this->valueCount; ++$i) {
            $B[$i] = [$yValues[$i]];
        }
        $matrixA = new Matrix($A);
        $matrixB = new Matrix($B);
        $C = $matrixA->solve($matrixB);

        $coefficients = [];
        for ($i = 0; $i < $C->getRowDimension(); ++$i) {
            $r = $C->get($i, 0);
            if (abs($r) <= 10 ** (-9)) {
                $r = 0;
            }
            $coefficients[] = $r;
        }

        $this->intersect = array_shift($coefficients);
        $this->slope = $coefficients;

        $this->calculateGoodnessOfFit($x_sum, $y_sum, $xx_sum, $yy_sum, $xy_sum, 0, 0, 0);
        foreach ($this->xValues as $xKey => $xValue) {
            $this->yBestFitValues[$xKey] = $this->getValueOfYForX($xValue);
        }
    }

    /**
     * Define the regression and calculate the goodness of fit for a set of X and Y data values.
     *
     * @param int $order Order of Polynomial for this regression
     * @param float[] $yValues The set of Y-values for this regression
     * @param float[] $xValues The set of X-values for this regression
     */
    public function __construct($order, $yValues, $xValues = [])
    {
        parent::__construct($yValues, $xValues);

        if (!$this->error) {
            if ($order < $this->valueCount) {
                $this->bestFitType .= '_' . $order;
                $this->order = $order;
                $this->polynomialRegression($order, $yValues, $xValues);
                if (($this->getGoodnessOfFit() < 0.0) || ($this->getGoodnessOfFit() > 1.0)) {
                    $this->error = true;
                }
            } else {
                $this->error = true;
            }
        }
    }
}
