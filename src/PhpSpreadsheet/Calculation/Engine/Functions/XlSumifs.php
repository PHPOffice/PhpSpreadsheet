<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional;

/**
 * @inheritDoc
 */
class XlSumifs extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SUMIFS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Conditional::class, 'SUMIFS'];

    /**
     * @var string
     */
    protected $argumentCount = '3+';
}
