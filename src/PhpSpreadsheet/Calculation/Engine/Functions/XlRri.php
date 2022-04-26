<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single;

/**
 * @inheritDoc
 */
class XlRri extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'RRI';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Single::class, 'interestRate'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
