<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

class Constants
{
    public const BASIS_DAYS_PER_YEAR_NASD = 0;
    public const BASIS_DAYS_PER_YEAR_ACTUAL = 1;
    public const BASIS_DAYS_PER_YEAR_360 = 2;
    public const BASIS_DAYS_PER_YEAR_365 = 3;
    public const BASIS_DAYS_PER_YEAR_360_EUROPEAN = 4;

    public const FREQUENCY_ANNUAL = 1;
    public const FREQUENCY_SEMI_ANNUAL = 2;
    public const FREQUENCY_QUARTERLY = 4;

    public const PAYMENT_END_OF_PERIOD = 0;
    public const PAYMENT_BEGINNING_OF_PERIOD = 1;
}
