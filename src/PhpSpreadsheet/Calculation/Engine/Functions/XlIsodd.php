<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;

/**
 * @inheritDoc
 */
class XlIsodd extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ISODD';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_INFORMATION;

    /**
     * @var callable
     */
    protected $functionCall = [Value::class, 'isOdd'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
