<?php

class Libro {

    private string $titulo;
    private string $autor;
    private string $genero;
    private bool $disponible;
    private string $id;

    // Constructor
    public function __construct($titulo, $autor, $genero, $disponible, $id){ 
        $this->titulo = $titulo ?? "";
        $this->autor = $autor ?? "";
        $this->genero = $genero ?? "";
        $this->disponible = $disponible ?? false;
        $this->id = $id ?? "";
        
    }

    // GET
    public function getTitulo() {
        return $this->titulo;
    }

    public function getAutor() {
        return $this->autor;
    }
    public function getGenero() {
        return $this->genero;
    }
    public function getDisponible() {
        return $this->disponible;
    }
    public function getID() {
        return $this->id;
    }
    //SET
    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }
    public function setAutor($autor) {
        $this->autor = $autor;
    }
    public function setGenero($genero) {
        $this->genero = $genero;
    }
    public function setDisponible($disponible) {
        $this->disponible = $disponible;
    }
    public function setID($id) {
        $this->id = $id;
    }


    // Devuelve libro con formato JSON
    public function toJson(): array {      
        return  [
                 'titulo' => $this->getTitulo(),
                 'autor' => $this->getAutor(),
                 'genero' => $this->getGenero(),
                 'disponible' => $this->getDisponible(),
                 'id' => $this->getID()
             ];
     }
}

?>