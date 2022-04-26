<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Sort;

/**
 * @inheritDoc
 */
class XlSort extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SORT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Sort::class, 'sort'];

    /**
     * @var string
     */
    protected $argumentCount = '1-4';
}
