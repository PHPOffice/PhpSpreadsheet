<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\InterestRate;

/**
 * @inheritDoc
 */
class XlEffect extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'EFFECT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [InterestRate::class, 'effective'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
