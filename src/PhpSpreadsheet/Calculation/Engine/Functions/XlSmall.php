<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Size;

/**
 * @inheritDoc
 */
class XlSmall extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SMALL';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Size::class, 'small'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
