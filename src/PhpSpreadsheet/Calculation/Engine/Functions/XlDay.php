<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateParts;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDay extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DAY';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATE_AND_TIME;

    /**
     * @var callable
     */
    protected $functionCall = [DateParts::class, 'day'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
