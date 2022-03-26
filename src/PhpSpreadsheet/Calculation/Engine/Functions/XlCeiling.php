<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Ceiling;

/**
 * @inheritDoc
 */
class XlCeiling extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CEILING';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Ceiling::class, 'ceiling'];

    /**
     * @var string
     */
    protected $argumentCount = '1-2';
}
