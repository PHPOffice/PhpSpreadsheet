<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateValue;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDatevalue extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DATEVALUE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATE_AND_TIME;

    /**
     * @var callable
     */
    protected $functionCall = [DateValue::class, 'fromString'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
