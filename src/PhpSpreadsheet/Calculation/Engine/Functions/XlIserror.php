<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;

/**
 * @inheritDoc
 */
class XlIserror extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ISERROR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [ErrorValue::class, 'isError'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
