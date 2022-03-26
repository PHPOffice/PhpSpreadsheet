<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles;

/**
 * @inheritDoc
 */
class XlPercentile extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PERCENTILE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Percentiles::class, 'PERCENTILE'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
