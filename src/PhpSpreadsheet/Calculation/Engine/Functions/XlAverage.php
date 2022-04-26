<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;

/**
 * @inheritDoc
 */
class XlAverage extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'AVERAGE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Averages::class, 'average'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
