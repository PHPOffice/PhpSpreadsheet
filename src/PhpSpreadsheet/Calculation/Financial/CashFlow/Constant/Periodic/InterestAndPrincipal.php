<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;

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
        int $type = FinancialConstants::PAYMENT_END_OF_PERIOD
    ) {
        $payment = Payments::annuity($rate, $numberOfPeriods, $presentValue, $futureValue, $type);
        $capital = $presentValue;
        $interest = 0.0;
        $principal = 0.0;
        for ($i = 1; $i <= $period; ++$i) {
            $interest = ($type === FinancialConstants::PAYMENT_BEGINNING_OF_PERIOD && $i == 1) ? 0 : -$capital * $rate;
            $principal = $payment - $interest;
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
