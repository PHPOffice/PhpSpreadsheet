<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Interest;

/**
 * @inheritDoc
 */
class XlIspmt extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ISPMT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Interest::class, 'schedulePayment'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
