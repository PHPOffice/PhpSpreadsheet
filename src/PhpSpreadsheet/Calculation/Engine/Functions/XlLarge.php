<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Size;

/**
 * @inheritDoc
 */
class XlLarge extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'LARGE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Size::class, 'large'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
