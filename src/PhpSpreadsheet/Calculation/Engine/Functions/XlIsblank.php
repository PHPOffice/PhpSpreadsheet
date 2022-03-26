<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;

/**
 * @inheritDoc
 */
class XlIsblank extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ISBLANK';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [Value::class, 'isBlank'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
