<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions;

/**
 * @inheritDoc
 */
class XlSequence extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SEQUENCE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [MatrixFunctions::class, 'sequence'];

    /**
     * @var string
     */
    protected $argumentCount = '1-4';
}
