<?php

class Prestamo {

    private string $libro_id;
    private string $usuario_id; 
    private string $fecha_inicio; 
    private string $id;
    private string $fecha_devolucion ;

    // Constructor
    public function __construct($libro, $usuario, $inicio, $id, $fin){       
        $this->libro_id = $libro ?? '';
        $this->usuario_id = $usuario ?? '';
        $this->fecha_inicio = $inicio ?? '';
        $this->id = $id ?? '';
        $this->fecha_devolucion = $fin ?? '';
    
    }

    // GET
    function getLibroID(){
        return $this->libro_id;
    }
    function getUsuarioID(){
        return $this->usuario_id;
    } 
    function getInicio(){
        return $this->fecha_inicio;
    } 
    function getPrestamoID(){
        return $this->id;
    } 
    function getFechaDev(){
        return $this->fecha_devolucion;
    }

    //SET
    function setFechaDev($devolucion) {
        $this->fecha_devolucion = $devolucion;
    }

    // Le da formato JSON y lo devuelve
    public function toJson(): array {      
        return  [
            'libro_id'=> $this->getLibroID(),
            'usuario_id'=> $this->getUsuarioID(), 
            'fecha_inicio'=> $this->getInicio(), 
            'id'=> $this->getPrestamoID(), 
            'fecha_devolucion'=> $this->getFechaDev()              
             ];
     }


}


?>