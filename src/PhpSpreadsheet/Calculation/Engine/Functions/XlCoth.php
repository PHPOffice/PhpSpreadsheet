<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cotangent;

/**
 * @inheritDoc
 */
class XlCoth extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COTH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Cotangent::class, 'coth'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
