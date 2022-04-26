<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Binomial;

/**
 * @inheritDoc
 */
class XlBinom_dist_range extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'BINOM.DIST.RANGE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Binomial::class, 'range'];

    /**
     * @var string
     */
    protected $argumentCount = '3,4';
}
