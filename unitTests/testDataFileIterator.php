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
        //    Read a line of test data from the file
        do {
            //    Only take lines that contain test data and that aren't commented out
            $testDataRow = trim(fgets($this->file));
        } while (($testDataRow > '') && ($testDataRow{0} === '#'));

        //    Discard any comments at the end of the line
        list($testData) = explode('//',$testDataRow);

        //    Split data into an array of individual values and a result
        $dataSet = str_getcsv($testData,',',"'");
        foreach($dataSet as &$dataValue) {
            $dataValue = $this->_parseDataValue($dataValue);
        }
        unset($dataValue);

        return $dataSet;
    }

    private function _parseDataValue($dataValue) {
        //    discard any white space
        $dataValue = trim($dataValue);
        //    test for the required datatype and convert accordingly
        if (!is_numeric($dataValue)) {
            if($dataValue == '') {
                $dataValue = NULL;
            } elseif($dataValue == '""') {
                $dataValue = '';
            } elseif(($dataValue[0] == '"') && ($dataValue[strlen($dataValue)-1] == '"')) {
                $dataValue = substr($dataValue,1,-1);
            } elseif(($dataValue[0] == '{') && ($dataValue[strlen($dataValue)-1] == '}')) {
                $dataValue = explode(';',substr($dataValue,1,-1));
                foreach($dataValue as &$dataRow) {
                    if (strpos($dataRow,'|') !== FALSE) {
                        $dataRow = explode('|',$dataRow);
                        foreach($dataRow as &$dataCell) {
                            $dataCell = $this->_parseDataValue($dataCell);
                        }
                        unset($dataCell);
                    } else {
                        $dataRow = $this->_parseDataValue($dataRow);
                    }
                }
                unset($dataRow);
            } else {
                switch (strtoupper($dataValue)) {
                    case 'NULL' :  $dataValue = NULL; break;
                    case 'TRUE' :  $dataValue = TRUE; break;
                    case 'FALSE' : $dataValue = FALSE; break;
                }
            }
        } else {
            if (strpos($dataValue,'.') !== FALSE) {
                $dataValue = (float) $dataValue;
            } else {
                $dataValue = (int) $dataValue;
            }
        }

		return $dataValue;
    }

}
