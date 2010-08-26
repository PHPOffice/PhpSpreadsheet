<?php

include "../Matrix.php";

/**
* Tiling of matrix X in [rowWise by colWise] dimension. Tiling
* creates a larger matrix than the original data X. Example, if
* X is to be tiled in a [3 x 4] manner, then:
*
*     /            \
*     | X  X  X  X |
* C = | X  X  X  X |
*     | X  X  X  X |
*     \           /
*
* @param X Matrix
* @param rowWise int
* @param colWise int
* @return Matrix
*/

function tile(&$X, $rowWise, $colWise){

  $xArray = $X->getArray();
  print_r($xArray);

  $countRow    = 0;
  $countColumn = 0;

  $m = $X->getRowDimension();
  $n = $X->getColumnDimension();

  if( $rowWise<1 || $colWise<1 ){
    die("tile : Array index is out-of-bound.");
  }

  $newRowDim = $m*$rowWise;
  $newColDim = $n*$colWise;

  $result = array();

  for($i=0 ; $i<$newRowDim; ++$i) {

    $holder = array();

    for($j=0 ; $j<$newColDim ; ++$j) {

      $holder[$j] = $xArray[$countRow][$countColumn++];

      // reset the column-index to zero to avoid reference to out-of-bound index in xArray[][]

      if($countColumn == $n) { $countColumn = 0; }

    } // end for

    ++$countRow;

    // reset the row-index to zero to avoid reference to out-of-bound index in xArray[][]

    if($countRow == $m) { $countRow = 0; }

    $result[$i] = $holder;

  } // end for

  return new Matrix($result);

}


$X =array(1,2,3,4,5,6,7,8,9);
$nRow = 3;
$nCol = 3;
$tiled_matrix = tile(new Matrix($X), $nRow, $nCol);
echo "<pre>";
print_r($tiled_matrix);
echo "</pre>";
?>
