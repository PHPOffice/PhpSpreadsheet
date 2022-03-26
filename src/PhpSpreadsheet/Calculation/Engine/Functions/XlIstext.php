<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;

/**
 * @inheritDoc
 */
class XlIstext extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ISTEXT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [Value::class, 'isText'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
