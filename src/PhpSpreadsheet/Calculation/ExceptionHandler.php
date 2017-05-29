<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;

class ExceptionHandler
{
    /**
     * Register errorhandler.
     */
    public function __construct()
    {
        set_error_handler([CalculationException::class, 'errorHandlerCallback'], E_ALL);
    }

    /**
     * Unregister errorhandler.
     */
    public function __destruct()
    {
        restore_error_handler();
    }
}
