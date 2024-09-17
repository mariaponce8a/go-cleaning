<?php
require_once('./back-end/config/conexion.php');

class Clase_Material
{
    public function todos()
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();

        $consulta = "SELECT * FROM tb_material"; 
        $resultado = mysqli_query($conexion, $consulta);
        
        if ($resultado === false) {
            throw new Exception(mysqli_error($conexion));
        }
        
        $material = array();
        while ($fila = mysqli_fetch_assoc($resultado)) {
            // Convierte la imagen a Base64 si existe
            if (!empty($fila['imagen']) && file_exists($fila['imagen'])) {
                $imagenBase64 = base64_encode(file_get_contents($fila['imagen']));
                $fila['imagen'] = 'data:image/jpeg;base64,' . $imagenBase64; // Ajusta el tipo MIME según sea necesario
            } else {
                $fila['imagen'] = null; // Manejar el caso donde no hay imagen
            }
            
            $material[] = $fila;
        }
        
        return $material;
    } catch (Exception $e) {
        error_log("Error en la consulta todos(): " . $e->getMessage());
        return false;
    } finally {
        if (isset($conexion)) {
            $conexion->close();
        }
    }
}


    public function insertar($descripcion_material, $imagenBase64)
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();
        
        // Decodifica la imagen Base64
        $imagen_data = base64_decode($imagenBase64);
        $imagen_nombre = uniqid() . '.png'; // Genera un nombre único para la imagen
        $imagen_ruta = './public/fotos_materiales/' . $imagen_nombre;

        // Guarda la imagen en el servidor
        if (!file_put_contents($imagen_ruta, $imagen_data)) {
            throw new Exception('Error al guardar la imagen.');
        }

        $consulta = "INSERT INTO tb_material (descripcion_material, imagen) VALUES (?, ?)";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("ss", $descripcion_material, $imagen_ruta);
        
        if ($stmt->execute()) {
            return "ok";
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Error al insertar material: " . $e->getMessage());
        return false;
    } finally {
        if (isset($conexion)) {
            $conexion->close();
        }
    }
}

public function actualizar($id_material, $descripcion_material, $imagenBase64 = null)
{
    try {
        $con = new Clase_Conectar();
        $conexion = $con->Procedimiento_Conectar();

        if ($imagenBase64 !== null) {
            $imagen_data = base64_decode($imagenBase64);
            $imagen_nombre = uniqid() . '.jpg'; // Genera un nombre único para la imagen
            $imagen_ruta = './public/fotos_materiales/' . $imagen_nombre;

            if (!file_put_contents($imagen_ruta, $imagen_data)) {
                throw new Exception('Error al guardar la imagen.');
            }

            // Actualiza el material con la nueva imagen
            $consulta = "UPDATE tb_material SET descripcion_material=?, imagen=? WHERE id_material=?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("ssi", $descripcion_material, $imagen_ruta, $id_material);
        } else {
            // Actualiza solo la descripción del material
            $consulta = "UPDATE tb_material SET descripcion_material=? WHERE id_material=?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("si", $descripcion_material, $id_material);
        }
        
        if ($stmt->execute()) {
            return "ok";
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        error_log("Error al actualizar material: " . $e->getMessage());
        return false;
    } finally {
        if (isset($conexion)) {
            $conexion->close();
        }
    }
}

    

    public function eliminar($id_material)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();
            
            $consulta = "DELETE FROM tb_material WHERE id_material=?";
            $stmt = $conexion->prepare($consulta);
            
            $stmt->bind_param("i", $id_material);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al eliminar material: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

    public function buscarPorId($id_material)
    {
        try {
            $con = new Clase_Conectar();
            $conexion = $con->Procedimiento_Conectar();

            $consulta = "SELECT * FROM tb_material WHERE id_material=?";
            $stmt = $conexion->prepare($consulta);
            $stmt->bind_param("i", $id_material);

            if ($stmt->execute()) {
                $resultado = $stmt->get_result();
                if ($resultado->num_rows === 1) {
                    $material = $resultado->fetch_assoc();
                    return $material;
                } else {
                    throw new Exception("No se encontró el material.");
                }
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error al buscar material por ID: " . $e->getMessage());
            return false;
        } finally {
            if (isset($conexion)) {
                $conexion->close();
            }
        }
    }

}