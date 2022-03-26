<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price;

/**
 * @inheritDoc
 */
class XlPrice extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PRICE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Price::class, 'price'];

    /**
     * @var string
     */
    protected $argumentCount = '6,7';
}
