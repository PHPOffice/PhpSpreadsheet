<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Ceiling;

/**
 * @inheritDoc
 */
class XlCeiling_math extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CEILING.MATH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Ceiling::class, 'math'];

    /**
     * @var string
     */
    protected $argumentCount = '1-3';
}
