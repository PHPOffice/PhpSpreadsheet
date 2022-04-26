<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosecant;

/**
 * @inheritDoc
 */
class XlCsc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CSC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Cosecant::class, 'csc'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
