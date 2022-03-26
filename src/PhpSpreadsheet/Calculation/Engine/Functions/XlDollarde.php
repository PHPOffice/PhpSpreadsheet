<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar;

/**
 * @inheritDoc
 */
class XlDollarde extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DOLLARDE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Dollar::class, 'decimal'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
