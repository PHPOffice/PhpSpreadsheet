<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Absolute;

/**
 * @inheritDoc
 */
class XlAbs extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ABS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Absolute::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
