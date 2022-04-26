<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Poisson;

/**
 * @inheritDoc
 */
class XlPoisson_dist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'POISSON.DIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Poisson::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
