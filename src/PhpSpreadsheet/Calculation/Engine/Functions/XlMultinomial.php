<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Factorial;

/**
 * @inheritDoc
 */
class XlMultinomial extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MULTINOMIAL';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Factorial::class, 'multinomial'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
