<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days360;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDays360 extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DAYS360';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATE_AND_TIME;

    /**
     * @var callable
     */
    protected $functionCall = [Days360::class, 'between'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
