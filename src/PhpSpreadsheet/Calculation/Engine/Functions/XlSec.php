<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Secant;

/**
 * @inheritDoc
 */
class XlSec extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SEC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Secant::class, 'sec'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
