<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Logarithms;

/**
 * @inheritDoc
 */
class XlLog10 extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'LOG10';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Logarithms::class, 'base10'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
