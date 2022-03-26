<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Operations;

/**
 * @inheritDoc
 */
class XlQuotient extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'QUOTIENT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Operations::class, 'quotient'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
