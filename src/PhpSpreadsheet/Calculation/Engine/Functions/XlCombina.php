<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Combinations;

/**
 * @inheritDoc
 */
class XlCombina extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COMBINA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Combinations::class, 'withRepetition'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
