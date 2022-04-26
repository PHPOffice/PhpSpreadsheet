<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Pi;

/**
 * @inheritDoc
 */
class XlPi extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PI';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Pi::class, 'pi'];

    /**
     * @var string
     */
    protected $argumentCount = '0';
}
