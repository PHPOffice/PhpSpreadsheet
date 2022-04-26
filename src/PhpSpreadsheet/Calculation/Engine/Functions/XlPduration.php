<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single;

/**
 * @inheritDoc
 */
class XlPduration extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PDURATION';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Single::class, 'periods'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
