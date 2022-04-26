<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Gcd;

/**
 * @inheritDoc
 */
class XlGcd extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'GCD';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Gcd::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
