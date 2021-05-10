<?php

return [
    // Basic arithmetic
    ['=D3+F7+G4+C6+5', 'of:=[.D3]+[.F7]+[.G4]+[.C6]+5'],
    ['=D3-F7-G4-C6-5', 'of:=[.D3]-[.F7]-[.G4]-[.C6]-5'],
    ['=D3*F7*G4*C6*5', 'of:=[.D3]*[.F7]*[.G4]*[.C6]*5'],
    ['=D3/F7/G4/C6/5', 'of:=[.D3]/[.F7]/[.G4]/[.C6]/5'],
    // Formulas
    ['=SUM(E1:E5)', 'of:=SUM([.E1:.E5])'],
    ['=SUM(E1:E5, D5)', 'of:=SUM([.E1:.E5], [.D5])'],
    ['=SUM(E1:E5, D5)-C5', 'of:=SUM([.E1:.E5], [.D5])-[.C5]'],
    ['=IF(E1>E2, E3, E4)', 'of:=IF([.E1]>[.E2], [.E3], [.E4])'],
    // String literals
    ['=CONCAT("Result of formula expression =[.C3]+[.C4] is: ", C3+C4)', 'of:=CONCAT("Result of formula expression =[.C3]+[.C4] is: ", [.C3]+[.C4])'],
];
