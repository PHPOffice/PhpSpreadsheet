<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

/**
 * @inheritDoc
 */
class XlTrue extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TRUE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOGICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Boolean::class, 'TRUE'];

    /**
     * @var string
     */
    protected $argumentCount = '0';
}
