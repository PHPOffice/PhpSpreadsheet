<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Price;

/**
 * @inheritDoc
 */
class XlReceived extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'RECEIVED';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Price::class, 'received'];

    /**
     * @var string
     */
    protected $argumentCount = '4-5';
}
