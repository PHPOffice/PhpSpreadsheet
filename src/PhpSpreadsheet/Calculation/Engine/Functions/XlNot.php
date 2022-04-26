<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations;

/**
 * @inheritDoc
 */
class XlNot extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NOT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOGICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Operations::class, 'NOT'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
