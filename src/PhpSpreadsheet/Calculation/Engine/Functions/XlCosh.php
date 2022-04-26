<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig\Cosine;

/**
 * @inheritDoc
 */
class XlCosh extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COSH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Cosine::class, 'cosh'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
