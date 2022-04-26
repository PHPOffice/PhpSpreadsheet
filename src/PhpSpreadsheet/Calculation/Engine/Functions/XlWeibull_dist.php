<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Weibull;

/**
 * @inheritDoc
 */
class XlWeibull_dist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'WEIBULL.DIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Weibull::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
