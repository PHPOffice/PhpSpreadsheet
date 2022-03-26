<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Floor;

/**
 * @inheritDoc
 */
class XlFloor extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'FLOOR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Floor::class, 'floor'];

    /**
     * @var string
     */
    protected $argumentCount = '1-2';
}
