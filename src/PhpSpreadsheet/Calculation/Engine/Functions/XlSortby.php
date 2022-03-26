<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Sort;

/**
 * @inheritDoc
 */
class XlSortby extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SORTBY';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Sort::class, 'sortBy'];

    /**
     * @var string
     */
    protected $argumentCount = '2+';
}
