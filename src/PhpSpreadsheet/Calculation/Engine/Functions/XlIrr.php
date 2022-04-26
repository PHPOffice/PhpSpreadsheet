<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Variable\Periodic;

/**
 * @inheritDoc
 */
class XlIrr extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IRR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Periodic::class, 'rate'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
