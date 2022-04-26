<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlPassCellReference;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Hyperlink;

/**
 * @inheritDoc
 */
class XlHyperlink extends XlFunctionAbstract
{
    use XlPassCellReference;

    /**
     * @var string
     */
    protected $name = 'HYPERLINK';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_LOOKUP_AND_REFERENCE;

    /**
     * @var callable
     */
    protected $functionCall = [Hyperlink::class, 'set'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
