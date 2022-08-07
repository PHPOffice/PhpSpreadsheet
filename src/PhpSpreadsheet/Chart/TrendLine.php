<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

class TrendLine extends Properties
{
    const TRENDLINE_EXPONENTIAL = 'exp';
    const TRENDLINE_LINEAR = 'linear';
    const TRENDLINE_LOGARITHMIC = 'log';
    const TRENDLINE_POLYNOMIAL = 'poly'; // + 'order'
    const TRENDLINE_POWER = 'power';
    const TRENDLINE_MOVING_AVG = 'movingAvg'; // + 'period'
    const TRENDLINE_TYPES = [
        self::TRENDLINE_EXPONENTIAL,
        self::TRENDLINE_LINEAR,
        self::TRENDLINE_LOGARITHMIC,
        self::TRENDLINE_POLYNOMIAL,
        self::TRENDLINE_POWER,
        self::TRENDLINE_MOVING_AVG,
    ];

    /** @var string */
    private $trendLineType = 'linear'; // TRENDLINE_LINEAR

    /** @var int */
    private $order = 2;

    /** @var int */
    private $period = 3;

    /** @var bool */
    private $dispRSqr = false;

    /** @var bool */
    private $dispEq = false;

    /**
     * Create a new TrendLine object.
     */
    public function __construct(string $trendLineType = '', ?int $order = null, ?int $period = null, bool $dispRSqr = false, bool $dispEq = false)
    {
        parent::__construct();
        $this->setTrendLineProperties($trendLineType, $order, $period, $dispRSqr, $dispEq);
    }

    public function getTrendLineType(): string
    {
        return $this->trendLineType;
    }

    public function setTrendLineType(string $trendLineType): self
    {
        $this->trendLineType = $trendLineType;

        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function setPeriod(int $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getDispRSqr(): bool
    {
        return $this->dispRSqr;
    }

    public function setDispRSqr(bool $dispRSqr): self
    {
        $this->dispRSqr = $dispRSqr;

        return $this;
    }

    public function getDispEq(): bool
    {
        return $this->dispEq;
    }

    public function setDispEq(bool $dispEq): self
    {
        $this->dispEq = $dispEq;

        return $this;
    }

    public function setTrendLineProperties(?string $trendLineType = null, ?int $order = 0, ?int $period = 0, ?bool $dispRSqr = false, ?bool $dispEq = false): self
    {
        if (!empty($trendLineType)) {
            $this->setTrendLineType($trendLineType);
        }
        if ($order !== null) {
            $this->setOrder($order);
        }
        if ($period !== null) {
            $this->setPeriod($period);
        }
        if ($dispRSqr !== null) {
            $this->setDispRSqr($dispRSqr);
        }
        if ($dispEq !== null) {
            $this->setDispEq($dispEq);
        }

        return $this;
    }
}
