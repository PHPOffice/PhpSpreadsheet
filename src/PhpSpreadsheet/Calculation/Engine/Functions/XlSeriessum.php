<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\SeriesSum;

/**
 * @inheritDoc
 */
class XlSeriessum extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SERIESSUM';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_MATH_AND_TRIG;

    /**
     * @var callable
     */
    protected $functionCall = [SeriesSum::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
