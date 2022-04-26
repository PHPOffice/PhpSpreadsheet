<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sqrt;

/**
 * @inheritDoc
 */
class XlSqrtpi extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SQRTPI';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Sqrt::class, 'pi'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
