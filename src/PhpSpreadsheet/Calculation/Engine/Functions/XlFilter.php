<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Filter;

/**
 * @inheritDoc
 */
class XlFilter extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'FILTER';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Filter::class, 'filter'];

    /**
     * @var string
     */
    protected $argumentCount = '2-3';
}
