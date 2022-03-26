<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Random;

/**
 * @inheritDoc
 */
class XlRandbetween extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'RANDBETWEEN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Random::class, 'randBetween'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
