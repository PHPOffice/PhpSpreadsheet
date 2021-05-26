<?php

return [
    'Basic addition' => ['=D3+F7+G4+C6+5', '=R[-2]C[-1]+R[2]C[1]+R[-1]C[2]+R[1]C[-2]+5', 5, 5],
    'Basic subtraction' => ['=D3-F7-G4-C6-5', '=R[-2]C[-1]-R[2]C[1]-R[-1]C[2]-R[1]C[-2]-5', 5, 5],
    'Basic multiplication' => ['=D3*F7*G4*C6*5', '=R[-2]C[-1]*R[2]C[1]*R[-1]C[2]*R[1]C[-2]*5', 5, 5],
    'Basic division' => ['=D3/F7/G4/C6/5', '=R[-2]C[-1]/R[2]C[1]/R[-1]C[2]/R[1]C[-2]/5', 5, 5],
    'Basic addition with current row/column' => ['=E3+E7+G5+C5+E5+5', '=R[-2]C+R[2]C+RC[2]+RC[-2]+RC+5', 5, 5],
    'Basic subtraction with current row/column' => ['=E3-E7-G5-C5-E5-5', '=R[-2]C-R[2]C-RC[2]-RC[-2]-RC-5', 5, 5],
    'Basic multiplication with current row/column' => ['=E3*E7*G5*C5*E5*5', '=R[-2]C*R[2]C*RC[2]*RC[-2]*RC*5', 5, 5],
    'Basic division with current row/column' => ['=E3/E7/G5/C5/E5/5', '=R[-2]C/R[2]C/RC[2]/RC[-2]/RC/5', 5, 5],
    'Simple formula' => ['=SUM(E1:E5)', '=SUM(R[-4]C:RC)', 5, 5],
    'Formula with range and cell' => ['=SUM(E1:E5, D5)', '=SUM(R[-4]C:RC, RC[-1])', 5, 5],
    'Formula arithmetic' => ['=SUM(E1:E5, D5)-C5', '=SUM(R[-4]C:RC, RC[-1])-RC[-2]', 5, 5],
    'Formula with comparison' => ['=IF(E1>E2, E3, E4)', '=IF(R[-4]C>R[-3]C, R[-2]C, R[-1]C)', 5, 5],
    'String literal' => ['=CONCAT("Result of formula expression =R[-2]C[-2]+R[-1]C[-2] is: ", C3+C4)', '=CONCAT("Result of formula expression =R[-2]C[-2]+R[-1]C[-2] is: ", R[-2]C[-2]+R[-1]C[-2])', 5, 5],
];
