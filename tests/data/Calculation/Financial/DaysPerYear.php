<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [360, 2020, FinancialConstants::BASIS_DAYS_PER_YEAR_NASD],
    [366, 2020, FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL],
    [365, 2021, FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL],
    [360, 2020, FinancialConstants::BASIS_DAYS_PER_YEAR_360],
    [365, 2020, FinancialConstants::BASIS_DAYS_PER_YEAR_365],
    [360, 2020, FinancialConstants::BASIS_DAYS_PER_YEAR_360_EUROPEAN],
    [ExcelError::NAN(), 2020, 'Invalid'],
    [ExcelError::NAN(), 2020, 999],
];
