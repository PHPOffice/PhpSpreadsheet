<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional;

/**
 * @inheritDoc
 */
class XlMaxifs extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MAXIFS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Conditional::class, 'MAXIFS'];

    /**
     * @var string
     */
    protected $argumentCount = '3+';
}
