<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;

/**
 * @inheritDoc
 */
class XlMedian extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MEDIAN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Averages::class, 'median'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
