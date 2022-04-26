<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

/**
 * @inheritDoc
 */
class XlExp extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'EXP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Exp::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
