<?php

class testDataFileIterator implements Iterator
{

    protected $file;
    protected $key = 0;
    protected $current;

    public function __construct($file)
    {
        $this->file = fopen($file, 'r');
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    public function rewind()
    {
        rewind($this->file);
        $this->current = $this->_parseNextDataset();
        $this->key = 0;
    }

    public function valid()
    {
        return !feof($this->file);
    }

    public function key()
    {
        return $this->key;
    }

    public function current()
    {
        return $this->current;
    }

    public function next()
    {
        $this->current = $this->_parseNextDataset();
        $this->key++;
    }

    private function _parseNextDataset()
    {
        $dataSet = explode(',',trim(fgets($this->file)));
        foreach($dataSet as &$dataValue) {
            if (!is_numeric($dataValue)) {
                if($dataValue == '') {
                    $dataValue = NULL;
                } elseif($dataValue == '""') {
                    $dataValue = '';
                } elseif(($dataValue[0] == '"') && ($dataValue[strlen($dataValue)-1] == '"')) {
                    $dataValue = substr($dataValue,1,-1);
                } else {
                    switch (strtoupper($dataValue)) {
                        case 'NULL' :  $dataValue = NULL; break;
                        case 'TRUE' :  $dataValue = TRUE; break;
                        case 'FALSE' : $dataValue = FALSE; break;
                    }
                }
            } else {
                if (is_float($dataValue)) {
                    $dataValue = (float) $dataValue;
                } else {
                    $dataValue = (int) $dataValue;
                }
            }
        }
        unset($dataValue);

        return $dataSet;
    }

}
