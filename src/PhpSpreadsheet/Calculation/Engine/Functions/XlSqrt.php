<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sqrt;

/**
 * @inheritDoc
 */
class XlSqrt extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SQRT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Sqrt::class, 'sqrt'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
