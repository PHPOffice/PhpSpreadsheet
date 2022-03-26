<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts;

/**
 * @inheritDoc
 */
class XlCount extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'COUNT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Counts::class, 'COUNT'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
