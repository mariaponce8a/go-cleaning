<?php

// Definición de la clase para la conexión a la base de datos
class Clase_Conectar
{
    // Propiedades para la conexión
    public $conexion;
    protected $db;
    private $server = "localhost";
    private $usu = "root";
    private $clave = "";  
    private $base = "proyectofinal";

    // Método para establecer la conexión
    public function Procedimiento_Conectar()
    {
        // Mostrar todos los errores de PHP
        error_reporting(E_ALL);
        
        // Establecer la conexión con mysqli
        $this->conexion = mysqli_connect($this->server, $this->usu, $this->clave, $this->base);
        if (!$this->conexion) {
            die("Error al conectarse con MySQL: " . mysqli_connect_error());
        }

        // Establecer el juego de caracteres UTF-8
        mysqli_set_charset($this->conexion, "utf8");
        
        // Seleccionar la base de datos
        $this->db = mysqli_select_db($this->conexion, $this->base);
        if (!$this->db) {
            die("Error conexión con la base de datos: " . mysqli_error($this->conexion));
        }

        return $this->conexion;
    }
}

// Crear una instancia de la clase y probar la conexión
$conectar = new Clase_Conectar();
$conectar->Procedimiento_Conectar();
?>
