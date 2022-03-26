<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlYearfrac extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'YEARFRAC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATE_AND_TIME;

    /**
     * @var callable
     */
    protected $functionCall = [YearFrac::class, 'fraction'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
