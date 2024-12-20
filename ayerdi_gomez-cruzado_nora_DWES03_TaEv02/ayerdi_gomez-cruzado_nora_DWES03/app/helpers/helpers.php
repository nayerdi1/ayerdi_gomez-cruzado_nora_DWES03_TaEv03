<?php
// Devuelve la respuesta al cliente con header y en formato Json
function respuestaJson($data, $codigo) {
    header('Content-Type: application/json');
    http_response_code($codigo);
    echo json_encode([
        'codigo' => $codigo,
        'respuesta' => $data
    ]);;
    exit;
}

// Convierte a  formato JSon
function toJson($array){
    $datosArray = [];
    foreach ($array as $dato) {
        $datosArray[] = $dato->toJson();
    }
    return $datosArray;
}

//Actualiza el Json con los cambios
function actualizarJson($array, $nombre){
    $jsonActualizado = json_encode([$nombre => $array], JSON_PRETTY_PRINT);
    if(file_put_contents("../app/models/data/".$nombre.".json", $jsonActualizado)){
        return true;
    }
    return false;
}

// Comprueba si ya tiene la sesion iniciada
function comprobarSesionIniciada($data, $usuarios){  
    foreach($usuarios as $usuario) {
        if($usuario->getNombre() == $data['usuario']){
            return $usuario->getSesion();                       
        }      
    }
    return false;
}  

?>