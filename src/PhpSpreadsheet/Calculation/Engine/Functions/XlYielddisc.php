<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Yields;

/**
 * @inheritDoc
 */
class XlYielddisc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'YIELDDISC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Yields::class, 'yieldDiscounted'];

    /**
     * @var string
     */
    protected $argumentCount = '4,5';
}
