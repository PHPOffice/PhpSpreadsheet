<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\CaseConvert;

/**
 * @inheritDoc
 */
class XlProper extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'PROPER';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [CaseConvert::class, 'proper'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
