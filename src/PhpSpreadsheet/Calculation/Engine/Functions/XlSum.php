<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

/**
 * @inheritDoc
 */
class XlSum extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SUM';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Sum::class, 'sumErroringStrings'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
