<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions;

/**
 * @inheritDoc
 */
class XlMunit extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MUNIT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [MatrixFunctions::class, 'identity'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
