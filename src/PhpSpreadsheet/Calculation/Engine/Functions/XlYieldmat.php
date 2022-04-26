<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Yields;

/**
 * @inheritDoc
 */
class XlYieldmat extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'YIELDMAT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Yields::class, 'yieldAtMaturity'];

    /**
     * @var string
     */
    protected $argumentCount = '5,6';
}
