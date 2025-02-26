<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Trend;

abstract class BestFit
{
    /**
     * Indicator flag for a calculation error.
     */
    protected bool $error = false;

    /**
     * Algorithm type to use for best-fit.
     */
    protected string $bestFitType = 'undetermined';

    /**
     * Number of entries in the sets of x- and y-value arrays.
     */
    protected int $valueCount;

    /**
     * X-value dataseries of values.
     *
     * @var float[]
     */
    protected array $xValues = [];

    /**
     * Y-value dataseries of values.
     *
     * @var float[]
     */
    protected array $yValues = [];

    /**
     * Flag indicating whether values should be adjusted to Y=0.
     */
    protected bool $adjustToZero = false;

    /**
     * Y-value series of best-fit values.
     *
     * @var float[]
     */
    protected array $yBestFitValues = [];

    protected float $goodnessOfFit = 1;

    protected float $stdevOfResiduals = 0;

    protected float $covariance = 0;

    protected float $correlation = 0;

    protected float $SSRegression = 0;

    protected float $SSResiduals = 0;

    protected float $DFResiduals = 0;

    protected float $f = 0;

    protected float $slope = 0;

    protected float $slopeSE = 0;

    protected float $intersect = 0;

    protected float $intersectSE = 0;

    protected float $xOffset = 0;

    protected float $yOffset = 0;

    public function getError(): bool
    {
        return $this->error;
    }

    public function getBestFitType(): string
    {
        return $this->bestFitType;
    }

    /**
     * Return the Y-Value for a specified value of X.
     *
     * @param float $xValue X-Value
     *
     * @return float Y-Value
     */
    abstract public function getValueOfYForX(float $xValue): float;

    /**
     * Return the X-Value for a specified value of Y.
     *
     * @param float $yValue Y-Value
     *
     * @return float X-Value
     */
    abstract public function getValueOfXForY(float $yValue): float;

    /**
     * Return the original set of X-Values.
     *
     * @return float[] X-Values
     */
    public function getXValues(): array
    {
        return $this->xValues;
    }

    /**
     * Return the Equation of the best-fit line.
     *
     * @param int $dp Number of places of decimal precision to display
     */
    abstract public function getEquation(int $dp = 0): string;

    /**
     * Return the Slope of the line.
     *
     * @param int $dp Number of places of decimal precision to display
     */
    public function getSlope(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->slope, $dp);
        }

        return $this->slope;
    }

    /**
     * Return the standard error of the Slope.
     *
     * @param int $dp Number of places of decimal precision to display
     */
    public function getSlopeSE(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->slopeSE, $dp);
        }

        return $this->slopeSE;
    }

    /**
     * Return the Value of X where it intersects Y = 0.
     *
     * @param int $dp Number of places of decimal precision to display
     */
    public function getIntersect(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->intersect, $dp);
        }

        return $this->intersect;
    }

    /**
     * Return the standard error of the Intersect.
     *
     * @param int $dp Number of places of decimal precision to display
     */
    public function getIntersectSE(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->intersectSE, $dp);
        }

        return $this->intersectSE;
    }

    /**
     * Return the goodness of fit for this regression.
     *
     * @param int $dp Number of places of decimal precision to return
     */
    public function getGoodnessOfFit(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->goodnessOfFit, $dp);
        }

        return $this->goodnessOfFit;
    }

    /**
     * Return the goodness of fit for this regression.
     *
     * @param int $dp Number of places of decimal precision to return
     */
    public function getGoodnessOfFitPercent(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->goodnessOfFit * 100, $dp);
        }

        return $this->goodnessOfFit * 100;
    }

    /**
     * Return the standard deviation of the residuals for this regression.
     *
     * @param int $dp Number of places of decimal precision to return
     */
    public function getStdevOfResiduals(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->stdevOfResiduals, $dp);
        }

        return $this->stdevOfResiduals;
    }

    /**
     * @param int $dp Number of places of decimal precision to return
     */
    public function getSSRegression(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->SSRegression, $dp);
        }

        return $this->SSRegression;
    }

    /**
     * @param int $dp Number of places of decimal precision to return
     */
    public function getSSResiduals(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->SSResiduals, $dp);
        }

        return $this->SSResiduals;
    }

    /**
     * @param int $dp Number of places of decimal precision to return
     */
    public function getDFResiduals(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->DFResiduals, $dp);
        }

        return $this->DFResiduals;
    }

    /**
     * @param int $dp Number of places of decimal precision to return
     */
    public function getF(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->f, $dp);
        }

        return $this->f;
    }

    /**
     * @param int $dp Number of places of decimal precision to return
     */
    public function getCovariance(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->covariance, $dp);
        }

        return $this->covariance;
    }

    /**
     * @param int $dp Number of places of decimal precision to return
     */
    public function getCorrelation(int $dp = 0): float
    {
        if ($dp != 0) {
            return round($this->correlation, $dp);
        }

        return $this->correlation;
    }

    /**
     * @return float[]
     */
    public function getYBestFitValues(): array
    {
        return $this->yBestFitValues;
    }

    protected function calculateGoodnessOfFit(float $sumX, float $sumY, float $sumX2, float $sumY2, float $sumXY, float $meanX, float $meanY, bool|int $const): void
    {
        $SSres = $SScov = $SStot = $SSsex = 0.0;
        foreach ($this->xValues as $xKey => $xValue) {
            $bestFitY = $this->yBestFitValues[$xKey] = $this->getValueOfYForX($xValue);

            $SSres += ($this->yValues[$xKey] - $bestFitY) * ($this->yValues[$xKey] - $bestFitY);
            if ($const === true) {
                $SStot += ($this->yValues[$xKey] - $meanY) * ($this->yValues[$xKey] - $meanY);
            } else {
                $SStot += $this->yValues[$xKey] * $this->yValues[$xKey];
            }
            $SScov += ($this->xValues[$xKey] - $meanX) * ($this->yValues[$xKey] - $meanY);
            if ($const === true) {
                $SSsex += ($this->xValues[$xKey] - $meanX) * ($this->xValues[$xKey] - $meanX);
            } else {
                $SSsex += $this->xValues[$xKey] * $this->xValues[$xKey];
            }
        }

        $this->SSResiduals = $SSres;
        $this->DFResiduals = $this->valueCount - 1 - ($const === true ? 1 : 0);

        if ($this->DFResiduals == 0.0) {
            $this->stdevOfResiduals = 0.0;
        } else {
            $this->stdevOfResiduals = sqrt($SSres / $this->DFResiduals);
        }

        if ($SStot == 0.0 || $SSres == $SStot) {
            $this->goodnessOfFit = 1;
        } else {
            $this->goodnessOfFit = 1 - ($SSres / $SStot);
        }

        $this->SSRegression = $this->goodnessOfFit * $SStot;
        $this->covariance = $SScov / $this->valueCount;
        $this->correlation = ($this->valueCount * $sumXY - $sumX * $sumY) / sqrt(($this->valueCount * $sumX2 - $sumX ** 2) * ($this->valueCount * $sumY2 - $sumY ** 2));
        $this->slopeSE = $this->stdevOfResiduals / sqrt($SSsex);
        $this->intersectSE = $this->stdevOfResiduals * sqrt(1 / ($this->valueCount - ($sumX * $sumX) / $sumX2));
        if ($this->SSResiduals != 0.0) {
            if ($this->DFResiduals == 0.0) {
                $this->f = 0.0;
            } else {
                $this->f = $this->SSRegression / ($this->SSResiduals / $this->DFResiduals);
            }
        } else {
            if ($this->DFResiduals == 0.0) {
                $this->f = 0.0;
            } else {
                $this->f = $this->SSRegression / $this->DFResiduals;
            }
        }
    }

    /** @return float|int */
    private function sumSquares(array $values)
    {
        return array_sum(
            array_map(
                fn ($value): float|int => $value ** 2,
                $values
            )
        );
    }

    /**
     * @param float[] $yValues
     * @param float[] $xValues
     */
    protected function leastSquareFit(array $yValues, array $xValues, bool $const): void
    {
        // calculate sums
        $sumValuesX = array_sum($xValues);
        $sumValuesY = array_sum($yValues);
        $meanValueX = $sumValuesX / $this->valueCount;
        $meanValueY = $sumValuesY / $this->valueCount;
        $sumSquaresX = $this->sumSquares($xValues);
        $sumSquaresY = $this->sumSquares($yValues);
        $mBase = $mDivisor = 0.0;
        $xy_sum = 0.0;
        for ($i = 0; $i < $this->valueCount; ++$i) {
            $xy_sum += $xValues[$i] * $yValues[$i];

            if ($const === true) {
                $mBase += ($xValues[$i] - $meanValueX) * ($yValues[$i] - $meanValueY);
                $mDivisor += ($xValues[$i] - $meanValueX) * ($xValues[$i] - $meanValueX);
            } else {
                $mBase += $xValues[$i] * $yValues[$i];
                $mDivisor += $xValues[$i] * $xValues[$i];
            }
        }

        // calculate slope
        $this->slope = $mBase / $mDivisor;

        // calculate intersect
        $this->intersect = ($const === true) ? $meanValueY - ($this->slope * $meanValueX) : 0.0;

        $this->calculateGoodnessOfFit($sumValuesX, $sumValuesY, $sumSquaresX, $sumSquaresY, $xy_sum, $meanValueX, $meanValueY, $const);
    }

    /**
     * Define the regression.
     *
     * @param float[] $yValues The set of Y-values for this regression
     * @param float[] $xValues The set of X-values for this regression
     */
    public function __construct(array $yValues, array $xValues = [])
    {
        //    Calculate number of points
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        //    Define X Values if necessary
        if ($xValueCount === 0) {
            $xValues = range(1, $yValueCount);
        } elseif ($yValueCount !== $xValueCount) {
            //    Ensure both arrays of points are the same size
            $this->error = true;
        }

        $this->valueCount = $yValueCount;
        $this->xValues = $xValues;
        $this->yValues = $yValues;
    }
}
