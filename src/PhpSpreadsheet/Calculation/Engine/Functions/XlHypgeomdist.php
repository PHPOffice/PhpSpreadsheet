<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\HyperGeometric;

/**
 * @inheritDoc
 */
class XlHypgeomdist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'HYPGEOMDIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [HyperGeometric::class, 'distribution'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
