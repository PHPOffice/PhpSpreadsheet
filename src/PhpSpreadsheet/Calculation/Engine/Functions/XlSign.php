<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sign;

/**
 * @inheritDoc
 */
class XlSign extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SIGN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Sign::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
