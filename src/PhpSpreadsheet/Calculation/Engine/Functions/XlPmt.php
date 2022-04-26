<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;

/**
 * @inheritDoc
 */
class XlPmt extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PMT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Payments::class, 'annuity'];

    /**
     * @var string
     */
    protected $argumentCount = '3-5';
}
