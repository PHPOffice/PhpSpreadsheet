<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price;

/**
 * @inheritDoc
 */
class XlPricemat extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PRICEMAT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Price::class, 'priceAtMaturity'];

    /**
     * @var string
     */
    protected $argumentCount = '5,6';
}
