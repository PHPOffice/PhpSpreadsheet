<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SumSquares;

/**
 * @inheritDoc
 */
class XlSumx2py2 extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SUMX2PY2';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [SumSquares::class, 'sumXSquaredPlusYSquared'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
