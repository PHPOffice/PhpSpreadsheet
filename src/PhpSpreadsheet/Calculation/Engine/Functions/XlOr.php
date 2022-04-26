<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations;

/**
 * @inheritDoc
 */
class XlOr extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'OR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOGICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Operations::class, 'logicalOr'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
