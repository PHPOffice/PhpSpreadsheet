<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional;

/**
 * @inheritDoc
 */
class XlIfna extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IFNA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOGICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Conditional::class, 'IFNA'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
