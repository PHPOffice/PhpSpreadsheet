<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix;

/**
 * @inheritDoc
 */
class XlIndex extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'INDEX';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Matrix::class, 'index'];

    /**
     * @var string
     */
    protected $argumentCount = '1-4';
}
