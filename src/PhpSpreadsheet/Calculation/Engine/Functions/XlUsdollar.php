<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar;

/**
 * @inheritDoc
 */
class XlUsdollar extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'USDOLLAR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Dollar::class, 'format'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
