<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StandardNormal;

/**
 * @inheritDoc
 */
class XlNorm_s_inv extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NORM.S.INV';

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
