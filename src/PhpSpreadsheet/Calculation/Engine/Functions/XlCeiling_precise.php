<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Ceiling;

/**
 * @inheritDoc
 */
class XlCeiling_precise extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CEILING.PRECISE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Ceiling::class, 'precise'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
