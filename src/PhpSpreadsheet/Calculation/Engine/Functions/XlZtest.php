<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal;

/**
 * @inheritDoc
 */
class XlZtest extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ZTEST';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [StandardNormal::class, 'zTest'];

    /**
     * @var string
     */
    protected $argumentCount = '2-3';
}
