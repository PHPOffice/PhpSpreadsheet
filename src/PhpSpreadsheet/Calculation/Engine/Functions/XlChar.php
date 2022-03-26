<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\CharacterConvert;

/**
 * @inheritDoc
 */
class XlChar extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CHAR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [CharacterConvert::class, 'character'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
