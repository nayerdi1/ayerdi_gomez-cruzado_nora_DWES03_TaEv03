<?php

require_once __DIR__ .'/../app/core/router.php';
require_once __DIR__ .'/../app/controllers/Admin.php';
require_once __DIR__ .'/../app/controllers/Libros.php';
require_once __DIR__ .'/../app/controllers/Prestamos.php';
require_once __DIR__ .'/../app/controllers/Usuarios.php';
require_once __DIR__ . '/../app/helpers/helpers.php'; 



$url = $_SERVER['QUERY_STRING'];
//echo $url;
//$baseUrl = '/ayerdi_gomez-cruzado_nora_DWES03';  // La parte inicial que quieres eliminar

//Reemplazar la baseUrl por una cadena vacía
//$restoUrl = str_replace($baseUrl, '', $url);


$router = new Router();
$router -> add('public/login', array (
    'controller' => 'Usuarios',
    'action' => 'login'
));
$router -> add('public/salir', array (
    'controller' => 'Usuarios',
    'action' => 'salir'
));
$router -> add('public/libros', array (
    'controller' => 'Libros',
    'action' => 'consultarLibros'
));
$router -> add('public/libros/{id}', array (
    'controller' => 'Libros',
    'action' => 'consultarLibroId'
));
$router -> add('public/prestamos', array (
    'controller' => 'Prestamos',
    'action' => 'prestamo'
));
$router -> add('public/prestamos/{id}', array (
    'controller' => 'Prestamos',
    'action' => 'devolucion'
));
$router -> add('public/admin/create', array (
    'controller' => 'Admin',
    'action' => 'aniadirLibro'
));
$router -> add('public/admin/update/{id}', array (
    'controller' => 'Admin',
    'action' => 'modificarLibro'
));
$router -> add('public/admin/delete/{id}', array (
    'controller' => 'Admin',
    'action' => 'borrarLibro'
));


if ($router->matchRoutes($url)) {
    
    $method = $_SERVER['REQUEST_METHOD'];
    $params = [];
    
    if($method === 'GET' ) {
        // Agrega el id al arreglo de parámetros
        $params['id'] = $router->getParams()['id'] ?? NULL;
        $params[] = intval($url) ?? NULL;

    } elseif($method === 'POST') {
        $json = file_get_contents('php://input');  
        $params[] = json_decode($json, true);
    
    } elseif($method === 'PUT' || $method === 'DELETE') {
        $params['id'] = $router->getParams()['id'] ?? NULL;   
        $json = file_get_contents('php://input');
        $params[] = json_decode($json, true);
        $params[] = intval($url) ?? NULL;

    } 
    $controller = $router -> getParams()['controller'];
    $action = $router -> getParams()['action'];
    $controller = new $controller();

    //var_dump($params);
    
    if(method_exists($controller, $action)) {
        call_user_func_array([$controller, $action], array_values($params));
    } else {
        respuestaJson(['error' => 'El metodo no existe'], 404);
        
    }

}else {
    respuestaJson(['error' => 'El Endpoint no existe'], 404);
    //echo "El metodo NO existe";
}


/*function respuestaJson($data, $codigo) {
    header('Content-Type: application/json');
    http_response_code($codigo);
    echo json_encode([
        'codigo' => $codigo,
        'respuesta' => $data
    ]);;
    exit;
}*/

?>