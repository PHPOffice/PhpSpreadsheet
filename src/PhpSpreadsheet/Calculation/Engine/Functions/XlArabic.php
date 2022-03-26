<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Arabic;

/**
 * @inheritDoc
 */
class XlArabic extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ARABIC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [Arabic::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
