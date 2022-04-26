<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Cumulative;

/**
 * @inheritDoc
 */
class XlCumipmt extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CUMIPMT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Cumulative::class, 'interest'];

    /**
     * @var string
     */
    protected $argumentCount = '6';
}
