<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;

/**
 * @inheritDoc
 */
class XlOdd extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ODD';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Round::class, 'odd'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
