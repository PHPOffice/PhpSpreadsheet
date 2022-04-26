<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;

/**
 * @inheritDoc
 */
class XlRoundup extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ROUNDUP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Round::class, 'up'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
