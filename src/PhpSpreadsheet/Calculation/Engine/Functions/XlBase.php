<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Base;

/**
 * @inheritDoc
 */
class XlBase extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BASE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Base::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
