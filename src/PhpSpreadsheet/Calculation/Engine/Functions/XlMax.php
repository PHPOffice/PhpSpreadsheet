<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum;

/**
 * @inheritDoc
 */
class XlMax extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MAX';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Maximum::class, 'max'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
