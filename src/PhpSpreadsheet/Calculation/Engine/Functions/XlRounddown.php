<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;

/**
 * @inheritDoc
 */
class XlRounddown extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ROUNDDOWN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Round::class, 'down'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
