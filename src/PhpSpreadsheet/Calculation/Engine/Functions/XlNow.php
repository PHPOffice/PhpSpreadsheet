<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlNow extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NOW';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATE_AND_TIME;

    /**
     * @var callable
     */
    protected $functionCall = [Current::class, 'now'];

    /**
     * @var string
     */
    protected $argumentCount = '0';
}
