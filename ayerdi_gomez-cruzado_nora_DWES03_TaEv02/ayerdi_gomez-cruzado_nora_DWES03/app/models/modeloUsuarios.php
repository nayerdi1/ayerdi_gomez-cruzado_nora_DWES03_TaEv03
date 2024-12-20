<?php

class Usuario
{
    //private array $usuarios = [];
    private string $id ;
    private string $nombre;
    private string $rol;
    private string $password;
    private bool $sesion_iniciada;

    // Constructor
    public function __construct($id, $nombre, $password, $rol,  $sesion) {      
        $this->id = $id ?? '';
        $this->nombre = $nombre ?? '';
        $this->rol = $rol ?? '';
        $this->password = $password ?? '';
        $this->sesion_iniciada = $sesion ?? '';         
    }

    // Get
    public function getId(): ?string{
        return $this->id;
    }
    public function getNombre() {
        return $this->nombre;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getRol(): ?string{
        return $this->rol;
    }

    public function getSesion(){
        return $this->sesion_iniciada;
    } 

    // Set
    public function setId($id) {
        $this->id = $id;
    }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    public function setPassword($password) {
        $this->password = $password;
    }
    public function setRol($rol) {
        $this->rol = $rol;
    }
    public function setSesion($sesion) {
        $this->sesion_iniciada = $sesion;
    }
   
    // Convierte el Usuario a formato JSON
    public function toJson(){      
       return  [
                'id' => $this->getId(),
                'usuario' => $this->getNombre(),
                'password' => $this->getPassword(),
                'rol' => $this->getRol(),
                'sesion_iniciada' => $this->getSesion()
            ];
    }

}


?>