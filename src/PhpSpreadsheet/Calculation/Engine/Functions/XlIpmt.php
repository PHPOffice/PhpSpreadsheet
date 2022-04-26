<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Interest;

/**
 * @inheritDoc
 */
class XlIpmt extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IPMT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Interest::class, 'payment'];

    /**
     * @var string
     */
    protected $argumentCount = '4-6';
}
