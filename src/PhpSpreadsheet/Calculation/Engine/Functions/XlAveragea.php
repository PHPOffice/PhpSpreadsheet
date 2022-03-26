<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;

/**
 * @inheritDoc
 */
class XlAveragea extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'AVERAGEA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Averages::class, 'averageA'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
