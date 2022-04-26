<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions;

/**
 * @inheritDoc
 */
class XlMdeterm extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MDETERM';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [MatrixFunctions::class, 'determinant'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
