<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate;

/**
 * @inheritDoc
 */
class XlTextjoin extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TEXTJOIN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Concatenate::class, 'TEXTJOIN'];

    /**
     * @var string
     */
    protected $argumentCount = '3+';
}
