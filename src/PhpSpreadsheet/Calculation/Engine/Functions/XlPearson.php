<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Trends;

/**
 * @inheritDoc
 */
class XlPearson extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PEARSON';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Trends::class, 'CORREL'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
