<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Conditional;

/**
 * @inheritDoc
 */
class XlIfs extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'IFS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOGICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Conditional::class, 'IFS'];

    /**
     * @var string
     */
    protected $argumentCount = '2+';
}
