<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional;

/**
 * @inheritDoc
 */
class XlIferror extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IFERROR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOGICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Conditional::class, 'IFERROR'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
