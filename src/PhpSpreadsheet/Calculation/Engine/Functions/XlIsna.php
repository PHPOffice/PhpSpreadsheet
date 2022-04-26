<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;

/**
 * @inheritDoc
 */
class XlIsna extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ISNA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [ErrorValue::class, 'isNa'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
