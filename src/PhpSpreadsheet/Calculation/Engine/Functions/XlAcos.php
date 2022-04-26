<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosine;

/**
 * @inheritDoc
 */
class XlAcos extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ACOS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Cosine::class, 'acos'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
