<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Angle;

/**
 * @inheritDoc
 */
class XlRadians extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'RADIANS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Angle::class, 'toRadians'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
