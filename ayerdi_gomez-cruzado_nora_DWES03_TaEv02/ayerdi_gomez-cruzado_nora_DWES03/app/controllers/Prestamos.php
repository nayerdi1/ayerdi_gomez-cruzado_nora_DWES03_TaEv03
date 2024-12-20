<?php
//session_start();

include_once __DIR__ . '/../models/modeloPrestamos.php';
include_once __DIR__ . '/../models/modeloLibros.php';
require_once __DIR__ . "/../models/modeloUsuarios.php";


class Prestamos {

    private array $prestamos;
    private array $usuarios;
    private array $libros;
    private string $PrestamoActualID = "";

    // Constructor
    function __construct(){
        $datosPrestamo = ['libro_id', 'usuario_id', 'fecha_inicio', 'id', 'fecha_devolucion'];
        $datosLibro = ['titulo', 'autor', 'genero', 'disponible', 'id'];
        $datosUsuario = ['id', 'usuario', 'password', 'rol', 'sesion_iniciada'];
        $this->prestamos = $this->cargarDatos("../app/models/data/prestamos.json", 'Prestamo', $datosPrestamo, 'prestamos');
        $this->usuarios = $this->cargarDatos("../app/models/data/usuarios.json", 'Usuario', $datosUsuario, 'usuarios');
        $this->libros = $this->cargarDatos("../app/models/data/libros.json", 'Libro', $datosLibro, 'libros');
        //var_dump($this->prestamos);
    }


    //--------------------FUNCIONES-------------------

    // Carga los datos del JSON y crea nuevas instancias de la clase modelo
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
    function getLibros() {
        return $this->libros;
    }
    function getUsuarios() {
        return $this->usuarios;
    }
    function getPrestamos() {
        return $this->prestamos;
    }
    public function getPrestamoActualID(): string {
        return $this->prestamoActualID;
    }
    // SET
    function setLibros($prestamo){
        $this->libros[] = $libro;
    }
    function setUsuarios($usuarios){
        $this->usuarios[] = $usuario;
    }
    function setPrestamos($prestamo){
        $this->prestamos[] = $prestamo;
    }
    function setPrestamoActualID($prestamo){
        $this->prestamoActualID = $prestamo;
    }


    //-------------Funciones llamadas desde el controlador frontal----------------

    // LLama al resto de funciones para crear el prestamo y devuelve la respuesta al cliente
    function prestamo($data) {
        if (comprobarSesionIniciada($data, $this->usuarios)){
            
            if($this->consultarLibroDisponible($data['libro_id'])) { 
                $idUsuario = $this->comprobarID($data); 
                $this->nuevoID();            
                $this->crearPrestamo($data, $idUsuario);
                if($this->cambiarDisponibilidad(false, $data['libro_id'])){
                        $this->guardarJson($librosArray=[], 'libros', $this->libros);
                        respuestaJson(['mensaje' => 'El prestamo se ha creado. Tu numero de prestamo es: '. $this->getPrestamoActualID()], 200);
                }              
            } else {
                respuestaJson(['error' => 'El libro no esta disponible'], 423);
            }
        }else {
            respuestaJson(['error' => 'Debe iniciar sesion para pedir el prestamo'], 401);
        }
    }

    // LLama al resto de funciones para procesar la devolucion y devuelve la respuesta al cliente
    function devolucion($id, $data) {
        if (comprobarSesionIniciada($data, $this->usuarios)){
                $idUsuario1 = $this->comprobarID($data);
                $idUsuario2 = $this->comprobarIDdevolucion($id);
                if($idUsuario1 == $idUsuario2){
                    if($data['accion'] === "devolver libro") {
                        if($this->modificarDevolucion($id)){     
                            respuestaJson(['mensaje' => 'La devolucion se ha registrado'], 200);
                        }                       
                    }
                } else{
                    respuestaJson(['error' => 'Este usuario no tenía prestado ese libro'], 400);
                }       
        }else {
            respuestaJson(['error' => 'Debe iniciar sesion para realizar la devolucion'], 401);
        }
    }


    //-------------Resto de funciones----------------

    // Devuelve si el libro indicado esta disponible
    function consultarLibroDisponible($id) {
        foreach ($this->getLibros() as $libro) {
            if($libro->getID() == $id) {
                return $libro->getDisponible();
            }
        }
        return false;
    }

    // Comprueba el nombre del usuario pasado en el Json y devuelve su ID
    function comprobarID($data){
        foreach($this->getUsuarios() as $usuario) {
            if($usuario->getNombre() == $data['usuario']){
                return $usuario->getId();
            }
        }
    }

    // Crea un  nuevo prestamo
    public function crearPrestamo($data, $idUsuario) {
        $prestamoCreado= new Prestamo(
            $data['libro_id'],
            $idUsuario,
            date('d-m-Y'),
            $this->getprestamoActualID(),
            null
        );      
        $this->setPrestamos($prestamoCreado);  
        $this->guardarJson($prestamosArray=[], 'prestamos', $this->prestamos);          
    }

    // Crea el nuevo ID para el prestamo
    public function nuevoID(){
        $ultimoPrestamo = end($this->prestamos);
        $ultimoID = $ultimoPrestamo->getPrestamoID();
        $this->setPrestamoActualID((string)((int)$ultimoID + 1));
    }

    // Cambia el libro disponible/no disponible
    function cambiarDisponibilidad($disponible, $id) {
        
        foreach($this->libros as &$libro) {
            if($libro->getID() == $id) {   
                $libro->setDisponible($disponible);
                return true;                        
            }
        }
        return false;        
    }

    // Comprueba que el array no este vacio y lo convierte a Json
    function guardarJSON($arrayNuevo, $nombre, $array) {
        $arrayNuevo = toJson($array);
        //var_dump($arrayNuevo);
        if(!empty($arrayNuevo)){
            if(actualizarJson($arrayNuevo, $nombre)){
                return true;
            }
        }
        return false;  
    }

    //Comprueba el ID del prestamo y devuelve el usuario al que se le presto
    public function comprobarIDdevolucion($id) {
        foreach($this->getPrestamos() as $prestamo){
            if($prestamo->getPrestamoID() == $id) {
                //$this->prestamoActualID($id);
                return $prestamo->getUsuarioID();
            }
        }
    }
    
    // Modifica el prestamo añadiendo la fecha actual en el campo "fecha_devolucion"
    //Si se modifica correctamente, cambia la disponibilidad del libro a true
    public function modificarDevolucion($id) {
        $libroID;
        foreach($this->getPrestamos() as $prestamo) {
            if($prestamo->getPrestamoID() == $id) {
                $prestamo->setFechaDev(date('d-m-Y'));
                $libroID = $prestamo->getLibroID();
                break;
            }
        } 
        if($this->guardarJSON($arrayPrestamos=[], 'prestamos', $this->prestamos)){
            $this->cambiarDisponibilidad(true, $libroID);
            return true;
        } else {
            return false;
        }         
    }
}


?>