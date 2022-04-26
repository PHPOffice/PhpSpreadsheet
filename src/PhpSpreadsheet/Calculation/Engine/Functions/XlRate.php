<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Interest;

/**
 * @inheritDoc
 */
class XlRate extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'RATE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Interest::class, 'rate'];

    /**
     * @var string
     */
    protected $argumentCount = '3-6';
}
