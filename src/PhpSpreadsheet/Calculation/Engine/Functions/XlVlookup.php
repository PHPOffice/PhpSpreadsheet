<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\VLookup;

/**
 * @inheritDoc
 */
class XlVlookup extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'VLOOKUP';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [VLookup::class, 'lookup'];

    /**
     * @var string
     */
    protected $argumentCount = '3,4';
}
