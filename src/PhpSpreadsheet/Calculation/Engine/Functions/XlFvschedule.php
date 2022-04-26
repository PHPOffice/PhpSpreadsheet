<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single;

/**
 * @inheritDoc
 */
class XlFvschedule extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'FVSCHEDULE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Single::class, 'futureValue'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
