<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;

/**
 * @inheritDoc
 */
class XlAvedev extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'AVEDEV';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Averages::class, 'averageDeviations'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
