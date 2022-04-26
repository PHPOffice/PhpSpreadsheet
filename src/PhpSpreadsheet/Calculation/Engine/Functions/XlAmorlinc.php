<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Amortization;

/**
 * @inheritDoc
 */
class XlAmorlinc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'AMORLINC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Amortization::class, 'AMORLINC'];

    /**
     * @var string
     */
    protected $argumentCount = '6,7';
}
