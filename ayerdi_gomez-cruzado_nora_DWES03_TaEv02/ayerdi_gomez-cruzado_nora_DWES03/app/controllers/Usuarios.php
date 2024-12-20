<?php

require_once __DIR__ . "/../models/modeloUsuarios.php";

class Usuarios {
    private array $usuarios = [];
    private Usuario $usuarioLog;
    //private Usuario $usuarios;

    //Constructor
    public function __construct() {
        $json = file_get_contents("../app/models/data/usuarios.json");
        $usuariosData = json_decode($json, true);
        
        foreach ($usuariosData['usuarios'] as $usuarioData) {
            $usuario = new Usuario(
                $usuarioData['id'],
                $usuarioData['usuario'],
                $usuarioData['password'],
                $usuarioData['rol'],
                $usuarioData['sesion_iniciada']
            );
            $this->usuarios[]= $usuario;
        }   
          
    }
    //GET
    public function getUsuarios(){
        return $this->usuarios;
    }
    //SET
    public function setUsuarios($usuario){
        $this->usuarios[]= $usuario;
    }


    //--------------Funciones llamadas desde el controlador frontal-------

    // Verifica el login del usuario 
    public function login($data) {
        
        if(!comprobarSesionIniciada($data, $this->usuarios)) {           
            if($this->comprobarUsuario($data)) {                              
                $this->usuarioLog->setSesion(true);
                $this->guardarJson();
                respuestaJson(['mensaje' => 'Ongi etorri'], 200);
                
            } else {
                respuestaJson(['error' => 'Usuario o contraseña incorrectos'], 401);
            }
        } else {
            respuestaJson(['error' => 'Ya tiene la sesion iniciada'], 409);
        }
      
    } 
     
    // Funcion para cerrar sesion del usuario
    public function salir($data) {
    
        if(comprobarSesionIniciada($data, $this->usuarios)) {
            if($this->comprobarUsuario($data)) { 
                $this->usuarioLog->setSesion(false);
                //$this->actualizarUsuarios();
                $this->guardarJson();

                respuestaJson(['mensaje' => 'Ikusi arte'], 200); 
            }    
        }else {
            respuestaJson(['error' => 'No tiene una sesion iniciada'], 409);
        }
    }

    //Comprueba si el usuario y contraseña introducidos son correctos
    function comprobarUsuario($data) {      
        foreach($this->usuarios as $usuario) {
            if($usuario->getNombre()=== $data['usuario'] && $usuario->getPassword()== $data['password']){
                $this->usuarioLog = $usuario;
                return true;
            }
        }
        return false;
    }

    // Guarda los cambios en el JSON
    function guardarJSON() {
        $usuariosArray = toJson($this->usuarios);
        actualizarJson($usuariosArray, 'usuarios');
    }
    
}




?>
