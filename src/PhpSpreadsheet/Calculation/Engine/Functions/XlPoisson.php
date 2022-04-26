<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Poisson;

/**
 * @inheritDoc
 */
class XlPoisson extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'POISSON';

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
