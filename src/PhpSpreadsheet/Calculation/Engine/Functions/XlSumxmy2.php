<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SumSquares;

/**
 * @inheritDoc
 */
class XlSumxmy2 extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SUMXMY2';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [SumSquares::class, 'sumXMinusYSquared'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
