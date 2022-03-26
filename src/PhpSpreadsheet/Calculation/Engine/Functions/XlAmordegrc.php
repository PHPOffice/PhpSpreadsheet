<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Amortization;

/**
 * @inheritDoc
 */
class XlAmordegrc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'AMORDEGRC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Amortization::class, 'AMORDEGRC'];

    /**
     * @var string
     */
    protected $argumentCount = '6,7';
}
