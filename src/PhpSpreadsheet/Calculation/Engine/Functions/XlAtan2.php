<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Tangent;

/**
 * @inheritDoc
 */
class XlAtan2 extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ATAN2';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Tangent::class, 'atan2'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
