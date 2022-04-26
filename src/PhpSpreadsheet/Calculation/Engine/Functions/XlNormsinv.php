<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal;

/**
 * @inheritDoc
 */
class XlNormsinv extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NORMSINV';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [StandardNormal::class, 'inverse'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
