<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\CaseConvert;

/**
 * @inheritDoc
 */
class XlLower extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'LOWER';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [CaseConvert::class, 'lower'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
