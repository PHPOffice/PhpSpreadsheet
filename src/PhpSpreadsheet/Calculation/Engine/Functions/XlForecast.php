<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends;

/**
 * @inheritDoc
 */
class XlForecast extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'FORECAST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Trends::class, 'FORECAST'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
