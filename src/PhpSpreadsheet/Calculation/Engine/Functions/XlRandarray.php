<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Random;

/**
 * @inheritDoc
 */
class XlRandarray extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'RANDARRAY';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Random::class, 'randArray'];

    /**
     * @var string
     */
    protected $argumentCount = '0-5';
}
