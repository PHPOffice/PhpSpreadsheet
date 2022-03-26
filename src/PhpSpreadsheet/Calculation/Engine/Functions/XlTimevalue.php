<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeValue;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlTimevalue extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TIMEVALUE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATE_AND_TIME;

    /**
     * @var callable
     */
    protected $functionCall = [TimeValue::class, 'fromString'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
