<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosecant;

/**
 * @inheritDoc
 */
class XlCsch extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CSCH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Cosecant::class, 'csch'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
