<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\IntClass;

/**
 * @inheritDoc
 */
class XlInt extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'INT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [IntClass::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
