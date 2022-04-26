<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\ExcelMatch;

/**
 * @inheritDoc
 */
class XlMatch extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MATCH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [ExcelMatch::class, 'MATCH'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
