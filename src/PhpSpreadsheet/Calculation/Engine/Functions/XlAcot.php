<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cotangent;

/**
 * @inheritDoc
 */
class XlAcot extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ACOT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Cotangent::class, 'acot'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
