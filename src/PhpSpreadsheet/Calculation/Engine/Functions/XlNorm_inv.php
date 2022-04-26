<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Normal;

/**
 * @inheritDoc
 */
class XlNorm_inv extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NORM.INV';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Normal::class, 'inverse'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
