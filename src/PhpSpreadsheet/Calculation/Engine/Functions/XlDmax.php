<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DMax;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;

/**
 * @inheritDoc
 */
class XlDmax extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DMAX';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_DATABASE;

    /**
     * @var callable
     */
    protected $functionCall = [DMax::class, 'evaluate'];

    /**
     * @var string
     */
    protected $argumentCount = '3';
}
