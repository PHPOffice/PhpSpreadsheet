<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Selection;

/**
 * @inheritDoc
 */
class XlChoose extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CHOOSE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Selection::class, 'CHOOSE'];

    /**
     * @var string
     */
    protected $argumentCount = '2+';
}
