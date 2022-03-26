<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Standardize;

/**
 * @inheritDoc
 */
class XlStandardize extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'STANDARDIZE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Standardize::class, 'execute'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
