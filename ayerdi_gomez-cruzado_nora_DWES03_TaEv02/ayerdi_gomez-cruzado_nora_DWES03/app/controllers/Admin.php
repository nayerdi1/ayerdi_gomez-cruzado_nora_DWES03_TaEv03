<?php

include_once __DIR__ . '/../models/modeloLibros.php';
require_once __DIR__ . "/../models/modeloUsuarios.php";

class Admin {


    private array $usuarios;
    private array $libros;

    // Constructor
     public function __construct(){  
        $datosLibro = ['titulo', 'autor', 'genero', 'disponible', 'id'];
        $datosUsuario = ['id', 'usuario', 'password', 'rol', 'sesion_iniciada'];
        
        $this->usuarios = $this->cargarDatos("../app/models/data/usuarios.json", 'Usuario', $datosUsuario, 'usuarios');
        $this->libros = $this->cargarDatos("../app/models/data/libros.json", 'Libro', $datosLibro, 'libros');
    }


    //--------------------FUNCIONES-------------------

    // Carga los datos del JSON a instancias de las clases modelo y lo guarda en un array
    private function cargarDatos($ruta, $clase, $datos, $nombre){
        $json = file_get_contents($ruta);
        $datosData = (json_decode($json, true)); 
        $instancias=[];

        foreach ($datosData[$nombre] as $datoData) {
            
            $parametros= [];
            foreach($datos as $dato){
                if (isset($datoData[$dato])) {
                    $parametros[] = $datoData[$dato];
                } else{
                    $parametros[] = null;
                }   
            }
            $instancias[]= new $clase(...$parametros);     
        } 
        return $instancias;
    }

    // GET
    public function getUsuarios(){
        return $this->usuarios;
    }

    public function getLibros(){
        return $this->libros;
    }

    // SET
    public function setUsuarios($usuario){
        $this->usuarios[]= $usuario;
    }
    public function setLibros($libro){
        $this->libros[]= $libro;
    }


    //--------------------Funciones llamadas desde el controlador frontal-------------------

    // Comprueba si el usuario tiene la sesion iniciada y si es Administrador
    // Añade el nuevo libro
    // Devuelve mensaje de exito o error
    function aniadirLibro($data) {

        if (comprobarSesionIniciada($data, $this->usuarios)){
            if($this->comprobarAdmin($data)){
                if($this->nuevoLibro($data)){
                    if($this->guardarJson()){
                        respuestaJson(['exito' => 'El libro se ha aniadido correctamente'], 200); 
                    }
                }                  
            } else {
                respuestaJson(['error' => 'no tiene permiso de administrador'], 401);
            }        
        }else {
            respuestaJson(['error' => 'Debe iniciar sesion para ver el catalogo'], 401);
        }
    }

    // Comprueba si el usuario tiene la sesion iniciada y si es Administrador
    // Modifica el libro
    // Devuelve mensaje de exito o error
    function modificarLibro($id, $data) {
   
        if (comprobarSesionIniciada($data, $this->usuarios)){
            if($this->comprobarAdmin($data)){
                if($this->modificar($id, $data)) {
                   
                    respuestaJson(['Exito' => 'El libro se ha modificado correctamente'], 200);
                } else{
                    respuestaJson(['error' => 'El libro no existe'], 404);
                }
            }else {
                respuestaJson(['error' => 'no tiene permiso de administrador'], 401);
            }
        }else {
            respuestaJson(['error' => 'Debe iniciar sesion para ver el catalogo'], 401);
        }
    }

    // Comprueba si el usuario tiene la sesion iniciada y si es Administrador
    // Borra el libro
    // Devuelve mensaje de exito o error
    function borrarLibro($id, $data) {

        if (comprobarSesionIniciada($data, $this->usuarios)){
            if($this->comprobarAdmin($data)){     
                if($this->borrar($id)) {
                    respuestaJson(['204' => 'El libro se ha borrado correctamente'], 200);
                } else{
                    respuestaJson(['error' => 'El libro no existe'], 404);
                }
            }else {
                respuestaJson(['error' => 'no tiene permiso de administrador'], 401);
            }
        }else {
            respuestaJson(['error' => 'Debe iniciar sesion para ver el catalogo'], 401);
        }
    }


    //--------------------Resto de funciones-------------------
    
    // Comprueba si el usuario tiene rol de administrador
    function comprobarAdmin($data){
        foreach($this->usuarios as $usuario) {
            if($data['usuario'] == $usuario->getNombre()){
                if($usuario->getRol() == "administrador"){
                    return true;           
                } 
            }      
        }
        return false;
    }

    // Crea nuevo Libro y lo añade al array libros. Lo guarda
    function nuevoLibro($data){
        $ultimo_libro = end($this->libros);
        $ultimoId = $ultimo_libro->getID();
        $nuevoLibro = new libro (
            $data['titulo'],
            $data['autor'],
            $data['genero'],
            $data['disponible'],
            sprintf("%03d", $ultimoId + 1)
        );
        $this->setLibros($nuevoLibro);
        return true;
              
    }
        
    // Modifica el libro indicado
    function modificar($id, $data) {
        foreach($this->libros as $num => $libro) {
            if($id == $libro->getID()) {
                $libro->setTitulo($data['titulo']);
                $libro->setAutor($data['autor']);
                $libro->setGenero($data['genero']);
                $libro->setDisponible($data['disponible']);
                $this->guardarJson();
                return true;                
            }
        }
        return false;
    }

    // Borra el libro indicado del array y guarda el cambio en la BD
    function borrar($id) {
        foreach($this->libros as $key => $libro) {
            if($id == $libro->getID()) {
                unset($this->libros[$key]);
                if($this->guardarJson()){
                    return true;
                }  
            }
        }
        return false;
    }

    // Convierte el array a formato JSON y lo guarda en el fichero 
    function guardarJSON() {
        $arrayLibros = toJson($this->libros);

        if(!empty($arrayLibros)){
            actualizarJson($arrayLibros, 'libros');
                return true;
            }else {
                return false;
            }      
    }

}




?>