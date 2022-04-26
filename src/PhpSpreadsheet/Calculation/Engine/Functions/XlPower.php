<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Operations;

/**
 * @inheritDoc
 */
class XlPower extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'POWER';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Operations::class, 'power'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
