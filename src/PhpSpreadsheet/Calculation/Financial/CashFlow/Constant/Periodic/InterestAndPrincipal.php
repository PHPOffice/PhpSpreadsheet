<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;

class InterestAndPrincipal
{
    protected $interest;

    protected $principal;

    public function __construct(
        float $rate = 0.0,
        int $period = 0,
        int $numberOfPeriods = 0,
        float $presentValue = 0,
        float $futureValue = 0,
        int $type = 0
    ) {
        $pmt = Payments::annuity($rate, $numberOfPeriods, $presentValue, $futureValue, $type);
        $capital = $presentValue;
        $interest = 0.0;
        $principal = 0.0;
        for ($i = 1; $i <= $period; ++$i) {
            $interest = ($type && $i == 1) ? 0 : -$capital * $rate;
            $principal = $pmt - $interest;
            $capital += $principal;
        }

        $this->interest = $interest;
        $this->principal = $principal;
    }

    public function interest(): float
    {
        return $this->interest;
    }

    public function principal(): float
    {
        return $this->principal;
    }
}
