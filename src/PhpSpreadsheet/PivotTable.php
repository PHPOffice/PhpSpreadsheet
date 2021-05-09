<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\PivotTable\PivotCacheDefinition;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotTable
{
    /**
     * PivotTable target.
     *
     * @var string
     */
    private $target = '';

    /**
     * PivotTable name.
     *
     * @var string
     */
    private $name = '';

    /**
     * PivotTable id.
     *
     * @var string
     */
    private $id = '';

    /**
     * PivotTable data.
     *
     * @var string
     */
    private $data = '';

    /**
     * Collection of PivotCacheDefinition.
     *
     * @var PivotCacheDefinition[]
     */
    private $pivotCacheDefinitionCollection = [];

    /**
     * Worksheet.
     *
     * @var Worksheet
     */
    private $worksheet;

    /**
     * Create a new PHPExcel_PivotTable.
     */
    public function __construct($id, $target, $xmlData)
    {
        $this->setId($id);
        $this->setTarget($target);
        // patch to correct  dxfId="XX"
        $this->data = preg_replace('/ dxfId=".."/i', '', $xmlData);
    }

    public function setId($id): void
    {
        $this->id = preg_replace('/^rId/i', '', $id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($target): void
    {
        $this->target = $target;
        $this->name = basename($target);
    }

    public function addPivotCacheDefinition(PivotCacheDefinition $PivotCacheDefinition): void
    {
        $this->pivotCacheDefinitionCollection[] = $PivotCacheDefinition;
    }

    public function getXmlData()
    {
        return $this->data;
    }

    public function setWorksheet(?Worksheet $pValue = null)
    {
        $this->worksheet = $pValue;

        return $this;
    }

    /**
     * @return PivotCacheDefinition[]
     */
    public function getPivotCacheDefinitionCollection(): array
    {
        return $this->pivotCacheDefinitionCollection;
    }

    public static function normalizePath($path): string
    {
        $parts = []; // Array to build a new path from the good parts
        $path = str_replace('\\', '/', $path); // Replace backslashes with forwardslashes
        $path = preg_replace('/\/+/', '/', $path); // Combine multiple slashes into a single slash
        $segments = explode('/', $path); // Collect path segments
        $test = ''; // Initialize testing variable

        foreach ($segments as $segment) {
            if ($segment != '.') {
                $test = array_pop($parts);
                if (null === $test) {
                    $parts[] = $segment;
                } elseif ($segment == '..') {
                    if ($test == '..') {
                        $parts[] = $test;
                    }
                    if ($test == '..' || $test == '') {
                        $parts[] = $segment;
                    }
                } else {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }

        return implode('/', $parts);
    }
}
