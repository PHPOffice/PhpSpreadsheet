<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons;

/**
 * @inheritDoc
 */
class XlCoupncd extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COUPNCD';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [Coupons::class, 'COUPNCD'];

    /**
     * @var string
     */
    protected $argumentCount = '3,4';
}
