<?php

namespace PhpSpreadsheet\Tests;

class TestDataFileIteratorJson implements \Iterator
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
        //    Read a line of test data from the file
        do {
            //    Only take lines that contain test data and that aren't commented out
            $testDataRow = trim(fgets($this->file));
        } while (($testDataRow > '') && ($testDataRow{0} === '#'));

        //    Discard any comments at the end of the line
        list($testData) = explode('//', $testDataRow);

        //    Split data into an array of individual values and a result
        $dataSet = json_decode(trim($testData));

        return $dataSet;
    }
}
