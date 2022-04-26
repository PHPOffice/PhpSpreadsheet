<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends;

/**
 * @inheritDoc
 */
class XlTrend extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TREND';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Trends::class, 'TREND'];

    /**
     * @var string
     */
    protected $argumentCount = '1-4';
}
