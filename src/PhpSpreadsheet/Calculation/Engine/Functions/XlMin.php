<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum;

/**
 * @inheritDoc
 */
class XlMin extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MIN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Minimum::class, 'min'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
