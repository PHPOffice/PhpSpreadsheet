<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles;

/**
 * @inheritDoc
 */
class XlPercentrank_inc extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PERCENTRANK.INC';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Percentiles::class, 'PERCENTRANK'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
