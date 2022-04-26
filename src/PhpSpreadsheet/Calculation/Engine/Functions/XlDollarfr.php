<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar;

/**
 * @inheritDoc
 */
class XlDollarfr extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DOLLARFR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Dollar::class, 'fractional'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
