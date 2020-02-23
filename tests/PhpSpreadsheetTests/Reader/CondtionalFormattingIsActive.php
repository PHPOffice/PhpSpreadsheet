<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PHPUnit\Framework\TestCase;

class CondNumFmtTest extends TestCase
{
    public function testCondtionalformattingIsActive()
    {
        $filename = './data/Reader/XLSX/ConditionalFormattingIsActiveTest.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
	$conditionalFormattings = $worksheet->getConditionalStylesCollection();
		
	// Taken from Excel 2013 manualy 
	$should=array( false, true, true, false, true, false, false, true, true, false, false, true, true, false, false, true, true, false, true, false, false, false, true, true);
		
	$actual=array();
		
	self::assertTrue(isset($conditionalFormattings));
	self::assertTrue(sizeof($conditionalFormattings) >0);
		
	foreach($conditionalFormattings as $key => $formatings){
		$split = explode(":", $key);
		$col = ord(substr($split[0], 0, 1)) ;				
		$multuseCol= false;
		
		if(sizeof($split)>1){
			$colEnd = ord(substr($split[0], 0, 1)) ;
		}else{
			$colEnd=$col;
		}
			
		$row = substr($split[0], 1);
						
		if(sizeof($split)>1){		
			$rowEnd = substr($split[1], 1);
		}else{
			$rowEnd=$row;
		}
			
		$multuseRow= false;
		for($i=$col; $i<=$colEnd; $i++){
			for($j=$row; $j<=$rowEnd; $j++){
				if(isset($formatings) && sizeof($formatings) > 0){
					foreach($formatings as $formating){
						if($col != $colEnd){
							$multuseCol =($col-$i)*(-1);
						}
						if($row != $rowEnd){
							$multuseRow =($row-$j)*(-1);
						}
						$active = $formating->isCondtionalStyleActive($spreadsheet,$worksheet, chr($i).$j, $multuseCol, $multuseRow );
						$actual[$j-1] = $active;
							
					}
				}
			}
		}
	}
		
	for($i = 0; $i < count($should); ++$i) {
		self::assertEquals($should[$i],$actual[$i]);
	}	
    }
}
