<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trunc;

/**
 * @inheritDoc
 */
class XlTrunc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TRUNC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Trunc::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
