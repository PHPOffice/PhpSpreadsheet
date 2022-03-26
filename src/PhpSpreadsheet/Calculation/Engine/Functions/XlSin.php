<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Sine;

/**
 * @inheritDoc
 */
class XlSin extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SIN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Sine::class, 'sin'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
