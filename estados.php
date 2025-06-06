<?php
require_once "utiles/funciones.php";
require_once "utiles/variables.php";

header("Access-Control-Allow-Origin: http://localhost/hlc/tarde/ProyectoIntegrado_2025/");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if($_SERVER["REQUEST_METHOD"] == "GET")
{
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    if(isset($_GET["id"]))
    {
        $consulta = "SELECT * FROM estados WHERE id = :id";
        $resultado = $conexion -> prepare($consulta);

        $resultado -> bindParam(":id", $_GET["id"]);

        $resultado -> execute();

        $estado = $resultado -> fetch(PDO::FETCH_ASSOC);


        echo json_encode($estado);
    }
    else
    {
        $consulta = "SELECT * FROM estados";

        $resultado = resultadoConsulta($conexion, $consulta);

        $estados = [];

        while($estado = $resultado -> fetch(PDO::FETCH_ASSOC))
        {
            $estados[] = $estado;
        }

        echo json_encode($estados);
    }
}

// Se usa POST para poder recoger la imagen, pero se hace UPDATE con el estado.
if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $datos = $_POST;

        $conexion = conectarPDO($host, $user, $password, $bbdd);

        $consulta = "SELECT imagen FROM publicaciones WHERE id = :id";
        $resultado = $conexion -> prepare($consulta);
        $resultado -> bindParam(":id", $datos["idPublicacion"]);
        $resultado -> execute();

        $publicacion = $resultado -> fetch(PDO::FETCH_ASSOC);
        $oldImg = $publicacion["imagen"];

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreOriginal = $_FILES['imagen']['name'];
            $temporal = $_FILES['imagen']['tmp_name'];

            // Se crea la carpeta si no existe.
            $carpeta = "uploads/";
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0755, true);
                
            }

            // Elimina la imagen anterior.
            unlink($oldImg); 
            $nuevoNombre = uniqid() . "_" . basename($nombreOriginal);
            $ruta = $carpeta . $nuevoNombre;

            if (move_uploaded_file($temporal, $ruta)) {
                $consulta = "UPDATE publicaciones 
                SET categoria_id = :categoria_id, nombre = :nombre, descripcion = :descripcion, imagen = :imagen, estado_id = :estado_id, updated_at = NOW() 
                WHERE id = :id";

                $resultado = $conexion -> prepare($consulta);

                $resultado -> bindParam(":categoria_id", $datos["categoria_id"]);
                $resultado -> bindParam(":nombre", $datos["nombre"]);
                $resultado -> bindParam(":descripcion", $datos["descripcion"]);
                $resultado -> bindParam(":imagen", $ruta);
                $resultado -> bindParam(":estado_id", $datos["estado_id"]);
                $resultado -> bindParam(":id", $datos["idPublicacion"]);

                try
                {
                    $resultado -> execute();

                    header($headerJSON);
                    echo json_encode(["mensaje" => "Publicación Actualizada.", "error" => false]);
                }
                catch(PDOException $e)
                {
                    header($headerJSON);
                    echo json_encode(["mensaje" => "Error: " . $e, "error" => true]);
                }
            } 
            else 
            {
                header($headerJSON);
                echo json_encode(["mensaje" => "Error: Error al mover la imagen.", "error" => true]);
            }
        }
    }
?>