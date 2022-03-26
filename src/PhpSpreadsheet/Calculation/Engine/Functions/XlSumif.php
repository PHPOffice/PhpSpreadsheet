<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional;

/**
 * @inheritDoc
 */
class XlSumif extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SUMIF';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Conditional::class, 'SUMIF'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
