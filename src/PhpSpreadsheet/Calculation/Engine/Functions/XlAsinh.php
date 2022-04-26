<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Sine;

/**
 * @inheritDoc
 */
class XlAsinh extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ASINH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Sine::class, 'asinh'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
