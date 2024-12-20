<?php


include_once __DIR__ . '/../models/modeloLibros.php';
require_once __DIR__ . "/../models/modeloUsuarios.php";

class Libros {

    private array $libros;

    // Constructor
    function __construct(){
        $json = file_get_contents("../app/models/data/libros.json");
        $librosData = (json_decode($json, true));
        foreach ($librosData['libros'] as $libroData) {
            $libro = new Libro(
                $libroData['titulo'],
                $libroData['autor'],
                $libroData['genero'],
                $libroData['disponible'],
                $libroData['id']
            );
            $this->libros[]= $libro;
        }    
    }

    // GET
    public function getLibros(){
        return $this->libros;
    }
    // SET
    public function setLibros($libro){
        $this->libros[] = $libro;
    }

    //-------------Funciones llamadas desde el controlador frontal----------------

    // Devuelve el catalogo de libros completo
    function consultarLibros() {
        $catalogo = $this->devolverCatalogo();

        respuestaJson(['Libros' => $catalogo], 200);    
    }

    // Devuelve info del libro indicado
    function consultarLibroId($id) {
            foreach($this->libros as $libro) {
                //var_dump($libro->getID());
                if($libro->getID() == $id){
                    respuestaJson(['Libro' => $libro->toJson()], 200);
                    //$libro->devolverLibros();
                }        
            }
            respuestaJson(['Error' => 'Libro no encontrado'], 404);
    }

    //-------------Resto de funciones----------------

    // LLama a funcion devolverLibro para darle estructura al catalogo
    function devolverCatalogo(){    
        foreach($this->libros as $libro) {

           $catalogoLibros[]= $libro->toJson();
        }
        return $catalogoLibros;
    
    }

}





?>