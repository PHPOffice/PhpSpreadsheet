<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Sine;

/**
 * @inheritDoc
 */
class XlAsin extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ASIN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Sine::class, 'asin'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
