<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert;

/**
 * @inheritDoc
 */
class XlUnicode extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'UNICODE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [CharacterConvert::class, 'code'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
