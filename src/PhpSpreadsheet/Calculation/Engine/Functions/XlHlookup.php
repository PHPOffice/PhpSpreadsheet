<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\HLookup;

/**
 * @inheritDoc
 */
class XlHlookup extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'HLOOKUP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [HLookup::class, 'lookup'];

    /**
     * @var string
     */
    protected $argumentCount = '3,4';
}
