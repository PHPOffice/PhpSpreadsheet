<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\RowColumnInformation;

/**
 * @inheritDoc
 */
class XlRows extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'ROWS';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [RowColumnInformation::class, 'ROWS'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
