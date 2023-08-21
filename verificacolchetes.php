<?php 

function suporteBalanceado($string) {
    $abertos = ['(', '{', '['];
    $fechados = [')', '}', ']'];
    $array = [];

    for ($i = 0; $i < strlen($string); $i++) {
        $char = $string[$i];
        
        if (in_array($char, $abertos)) {
            array_push($array, $char);
        } elseif (in_array($char, $fechados)) {
            $ultimoAberto = array_pop($array);
            $indiceUltimoAberto = array_search($ultimoAberto, $abertos);
            if ($char !== $fechados[$indiceUltimoAberto]) {
                return false; 
            }
        }
    }

    return empty($array);
}

// Exemplos de uso
echo suporteBalanceado("(){}[]") ? "Válido" : "Inválido";  // Válido
echo suporteBalanceado("[{()}](){}") ? "Válido" : "Inválido";  // Válido
echo suporteBalanceado("[]{()") ? "Válido" : "Inválido";  // Inválido
echo suporteBalanceado("[{)]") ? "Válido" : "Inválido";  // Inválido
