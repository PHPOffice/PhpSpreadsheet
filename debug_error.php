<?php
require __DIR__ . '/vendor/autoload.php';

function esFormulaValida(string $valor): bool {
    if (!str_starts_with($valor, '=')) {
        return false;
    }
    
    $cuerpo = substr($valor, 1);
    
    $tieneFuncion = preg_match('/[A-Z]+\s*\(/i', $cuerpo); // Ej: SUM(...), PROMEDIO(...)
    $tieneOperadores = preg_match('/[\+\-\*\/\^&<>]/', $cuerpo); // Ej: +, -, *, /
    $tieneReferenciaCelda = preg_match('/[A-Z]{1,3}[0-9]+/', $cuerpo); // Ej: A1, B12
    
    return $tieneFuncion || $tieneOperadores || $tieneReferenciaCelda;
}

$casosDePrueba = [
    '=SUM(A1:A10)',
    '=A1+B1',
    '=A1',
    '=TextoQueEmpiezaConIgualYNoEsFormula',
    'Hola mundo sin igual'
];

echo "--- PROBANDO NUESTRA HEURÍSTICA INTELIGENTE ---\n\n";

foreach ($casosDePrueba as $caso) {
    $esValida = esFormulaValida($caso);
    $veredicto = $esValida ? "✅ SÍ es fórmula válida" : "❌ NO es fórmula (Tratar como texto)";
    echo "Texto: {$caso}\nResultado: {$veredicto}\n-----------------------------------\n";
}