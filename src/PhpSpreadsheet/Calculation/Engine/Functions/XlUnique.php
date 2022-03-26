<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Unique;

/**
 * @inheritDoc
 */
class XlUnique extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'UNIQUE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Unique::class, 'unique'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
