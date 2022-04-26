<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\NetworkDays;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlNetworkdays extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NETWORKDAYS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATE_AND_TIME;

    /**
     * @var callable
     */
    protected $functionCall = [NetworkDays::class, 'count'];

    /**
     * @var string
     */
    protected $argumentCount = '2-3';
}
