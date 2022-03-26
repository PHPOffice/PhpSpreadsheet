<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles;

/**
 * @inheritDoc
 */
class XlPercentile_inc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PERCENTILE.INC';

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
