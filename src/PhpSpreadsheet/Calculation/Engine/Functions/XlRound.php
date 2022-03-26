<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Round;

/**
 * @inheritDoc
 */
class XlRound extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ROUND';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Round::class, 'round'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
