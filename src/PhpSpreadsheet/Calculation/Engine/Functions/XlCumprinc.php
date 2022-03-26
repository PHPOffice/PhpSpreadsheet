<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Cumulative;

/**
 * @inheritDoc
 */
class XlCumprinc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CUMPRINC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Cumulative::class, 'principal'];

    /**
     * @var string
     */
    protected $argumentCount = '6';
}
