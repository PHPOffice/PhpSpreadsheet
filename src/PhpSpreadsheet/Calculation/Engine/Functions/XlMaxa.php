<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Maximum;

/**
 * @inheritDoc
 */
class XlMaxa extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'MAXA';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_STATISTICAL;

    /**
     * @var callable
     */
    protected $functionCall = [Maximum::class, 'maxA'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
