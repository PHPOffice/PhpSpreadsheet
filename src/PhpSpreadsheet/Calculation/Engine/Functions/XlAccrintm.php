<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\AccruedInterest;

/**
 * @inheritDoc
 */
class XlAccrintm extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ACCRINTM';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_FINANCIAL;

    /**
     * @var callable
     */
    protected $functionCall = [AccruedInterest::class, 'atMaturity'];

    /**
     * @var string
     */
    protected $argumentCount = '3-5';
}
