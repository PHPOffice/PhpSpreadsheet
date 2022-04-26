<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

/**
 * @inheritDoc
 */
class XlFalse extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'FALSE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOGICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Boolean::class, 'FALSE'];

    /**
     * @var string
     */
    protected $argumentCount = '0';
}
