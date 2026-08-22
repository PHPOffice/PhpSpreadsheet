<?php

declare(strict_types=1);

return [
    'Basic addition' => ['=D3+F7+G4+C6+5', 'of:=[.D3]+[.F7]+[.G4]+[.C6]+5'],
    'Basic subtraction' => ['=D3-F7-G4-C6-5', 'of:=[.D3]-[.F7]-[.G4]-[.C6]-5'],
    'Basic multiplication' => ['=D3*F7*G4*C6*5', 'of:=[.D3]*[.F7]*[.G4]*[.C6]*5'],
    'Basic division' => ['=D3/F7/G4/C6/5', 'of:=[.D3]/[.F7]/[.G4]/[.C6]/5'],
    'Simple formula' => ['=SUM(E1:E5)', 'of:=SUM([.E1:.E5])'],
    'Formula with range and cell' => ['=SUM(E1:E5, D5)', 'of:=SUM([.E1:.E5], [.D5])'],
    'Formula arithmetic' => ['=SUM(E1:E5, D5)-C5', 'of:=SUM([.E1:.E5], [.D5])-[.C5]'],
    'Formula with comparison' => ['=IF(E1>E2, E3, E4)', 'of:=IF([.E1]>[.E2], [.E3], [.E4])'],
    'String literal' => ['=CONCAT("Result of formula expression =[.C3]+[.C4] is: ", C3+C4)', 'of:=CONCAT("Result of formula expression =[.C3]+[.C4] is: ", [.C3]+[.C4])'],
    'Simple numeric addition' => ['=1.23+2.34', 'of:=1.23+2.34'],
    'More complex formula with cells and numeric literals' => ['=D3+F7+G4+C6+5.67', 'of:=[.D3]+[.F7]+[.G4]+[.C6]+5.67'],
];
