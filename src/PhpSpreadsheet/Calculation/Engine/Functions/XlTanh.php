<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Tangent;

/**
 * @inheritDoc
 */
class XlTanh extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TANH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Tangent::class, 'tanh'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
