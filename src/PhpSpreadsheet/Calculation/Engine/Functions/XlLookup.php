<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Lookup;

/**
 * @inheritDoc
 */
class XlLookup extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'LOOKUP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Lookup::class, 'lookup'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
