<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Trend;

class PowerBestFit extends BestFit
{
    /**
     * Algorithm type to use for best-fit
     * (Name of this Trend class).
     *
     * @var string
     */
    protected $bestFitType = 'power';

    /**
     * Return the Y-Value for a specified value of X.
     *
     * @param float $xValue X-Value
     *
     * @return float Y-Value
     */
    public function getValueOfYForX($xValue)
    {
        return $this->getIntersect() * pow(($xValue - $this->xOffset), $this->getSlope());
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
        return pow((($yValue + $this->yOffset) / $this->getIntersect()), (1 / $this->getSlope()));
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

        return 'Y = ' . $intersect . ' * X^' . $slope;
    }

    /**
     * Return the Value of X where it intersects Y = 0.
     *
     * @param int $dp Number of places of decimal precision to display
     *
     * @return float
     */
    public function getIntersect($dp = 0)
    {
        if ($dp != 0) {
            return round(exp($this->intersect), $dp);
        }

        return exp($this->intersect);
    }

    /**
     * Execute the regression and calculate the goodness of fit for a set of X and Y data values.
     *
     * @param float[] $yValues The set of Y-values for this regression
     * @param float[] $xValues The set of X-values for this regression
     * @param bool $const
     */
    private function powerRegression($yValues, $xValues, $const)
    {
        foreach ($xValues as &$value) {
            if ($value < 0.0) {
                $value = 0 - log(abs($value));
            } elseif ($value > 0.0) {
                $value = log($value);
            }
        }
        unset($value);
        foreach ($yValues as &$value) {
            if ($value < 0.0) {
                $value = 0 - log(abs($value));
            } elseif ($value > 0.0) {
                $value = log($value);
            }
        }
        unset($value);

        $this->leastSquareFit($yValues, $xValues, $const);
    }

    /**
     * Define the regression and calculate the goodness of fit for a set of X and Y data values.
     *
     * @param float[] $yValues The set of Y-values for this regression
     * @param float[] $xValues The set of X-values for this regression
     * @param bool $const
     */
    public function __construct($yValues, $xValues = [], $const = true)
    {
        if (parent::__construct($yValues, $xValues) !== false) {
            $this->powerRegression($yValues, $xValues, $const);
        }
    }
}
