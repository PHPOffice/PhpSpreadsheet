<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Address;

/**
 * @inheritDoc
 */
class XlAddress extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ADDRESS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Address::class, 'cell'];

    /**
     * @var string
     */
    protected $argumentCount = '2-5';
}
