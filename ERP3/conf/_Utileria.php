
<?php
class Utileria{
function sql($arreglo,$slice = 0){
    if(!empty($arreglo)){
        if(isset($arreglo['opc'])) unset($arreglo['opc']);
        $sqlArray = [];

        
        if (is_array($arreglo) && is_array($arreglo[0])) {
            $sqlArray['values'] = array_keys(current($arreglo));
            foreach ($arreglo as $row) $sqlArray['data'][] = array_values($row);
        } else {
            foreach ($arreglo as $key => $value) {
                $sqlArray['values'][] = $key; // Obtenemos los index y los guardamos como values
                $sqlArray['data'][] =  ($value == '') ? null : $value; // Obtenemos los values que usamos para cada ?
            }
        }

        // Comprobamos que where exista
        if ($slice !== 0) {
            // Separamos los valores acorde a la cantidad de valores del where
            $sqlArray['where'] = array_slice($sqlArray['values'],-$slice);
            array_splice($sqlArray['values'],-$slice);
        }

        // if(count($sqlArray['values']) == 0 ) unset($sqlArray['values']);

        return $sqlArray;

    }
}

}