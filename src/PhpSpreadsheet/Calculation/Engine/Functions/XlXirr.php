<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable\NonPeriodic;

/**
 * @inheritDoc
 */
class XlXirr extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'XIRR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [NonPeriodic::class, 'rate'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
