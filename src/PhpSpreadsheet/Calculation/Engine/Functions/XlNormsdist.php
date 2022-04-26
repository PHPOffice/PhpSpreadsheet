<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal;

/**
 * @inheritDoc
 */
class XlNormsdist extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NORMSDIST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [StandardNormal::class, 'cumulative'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
