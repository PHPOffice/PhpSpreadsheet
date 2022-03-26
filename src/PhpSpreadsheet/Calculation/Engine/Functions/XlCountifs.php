<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Conditional;

/**
 * @inheritDoc
 */
class XlCountifs extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COUNTIFS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Conditional::class, 'COUNTIFS'];

    /**
     * @var string
     */
    protected $argumentCount = '2+';
}
