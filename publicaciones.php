<?php
    require_once "utiles/funciones.php";
    require_once "utiles/variables.php";

    header("Access-Control-Allow-Origin: http://localhost/hlc/tarde/ProyectoIntegrado_2025/");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");



    if($_SERVER["REQUEST_METHOD"] == "GET")
    {
        $conexion = conectarPDO($host, $user, $password, $bbdd);

        if(isset($_GET["id"])) // Búsqueda por id del usuario.
        {
            // Si el id es del admin, mostrará todas las publicaciones existentes.
            if($_GET["id"] == 1)
            {
                $consulta = "SELECT * FROM publicaciones ORDER BY estado_id";

                $resultado = resultadoConsulta($conexion, $consulta);

                $publicaciones = [];

                while($publicacion = $resultado -> fetch(PDO::FETCH_ASSOC))
                {
                    $publicaciones[] = $publicacion;
                }

                echo json_encode($publicaciones);
            }
            else
            {
                $consulta = "SELECT * FROM publicaciones WHERE usuario_id = :usuario_id ORDER BY estado_id";

                $resultado = $conexion -> prepare($consulta);
                $resultado -> bindParam(":usuario_id", $_GET["id"]);

                $resultado -> execute();
                if($resultado -> rowCount() != 0)
                {
                    while($publicacion = $resultado -> fetch(PDO::FETCH_ASSOC))
                    {
                        $publicaciones[] = $publicacion;
                    }
                    
                    echo json_encode($publicaciones);
                }
                else
                {
                    $datos = [];
                    //$datos["error"] = true;;  
                    
                    echo json_encode($datos);
                }
            }
        }
        else if(isset($_GET["idPublicacion"])) // Búsqueda por id de publicación.
        {

            $consulta = "SELECT * FROM publicaciones WHERE id = :id";

            $resultado = $conexion -> prepare($consulta);

            $resultado -> bindParam(":id", $_GET["idPublicacion"]);

            $resultado -> execute();

            $publicacion = $resultado -> fetch(PDO::FETCH_ASSOC);

            echo json_encode($publicacion);
        }
        else
        {
            $consulta = "SELECT * FROM publicaciones WHERE estado_id = 1";

            $resultado = resultadoConsulta($conexion, $consulta);

            $publicaciones = [];

            while($publicacion = $resultado -> fetch(PDO::FETCH_ASSOC))
            {
                $publicaciones[] = $publicacion;
            }

            echo json_encode($publicaciones);
        }
    }



    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $datos = $_POST;

        $conexion = conectarPDO($host, $user, $password, $bbdd);



        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreOriginal = $_FILES['imagen']['name'];
            $temporal = $_FILES['imagen']['tmp_name'];

            $carpeta = "uploads/";
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0755, true);
            }

            $nuevoNombre = uniqid() . "_" . basename($nombreOriginal);
            $ruta = $carpeta . $nuevoNombre;

            if (move_uploaded_file($temporal, $ruta)) {
                $consulta = "INSERT INTO publicaciones (usuario_id, categoria_id, nombre, descripcion, imagen, estado_id, created_at, updated_at) 
                            VALUES(:usuario_id, :categoria_id, :nombre, :descripcion, :imagen, 2, NOW(), NOW())";
        
                $resultado = $conexion -> prepare($consulta);

                $resultado -> bindParam(":usuario_id", $datos["usuario_id"]);
                $resultado -> bindParam(":categoria_id", $datos["categoria_id"]);
                $resultado -> bindParam(":nombre", $datos["nombre"]);
                $resultado -> bindParam(":descripcion", $datos["descripcion"]);
                $resultado -> bindParam(":imagen", $ruta);

                try
                {
                    $resultado -> execute();

                    header($headerJSON);
                    echo json_encode(["mensaje" => "Publicación Creada.", "error" => false]);
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



    if($_SERVER["REQUEST_METHOD"] == "DELETE")
    {
        $datos = json_decode(file_get_contents("php://input"), true);

        $conexion = conectarPDO($host, $user, $password, $bbdd);


        $consulta = "SELECT imagen FROM publicaciones WHERE id = :id";
        $resultado = $conexion -> prepare($consulta);
        $resultado -> bindParam(":id", $datos["idPublicacion"]);
        $resultado -> execute();
        $publicacion = $resultado -> fetch(PDO::FETCH_ASSOC);
        
        if ($publicacion && file_exists($publicacion['imagen'])) {
            unlink($publicacion['imagen']); // Elimina la imagen del servidor
        }

        
        $consulta = "DELETE FROM publicaciones WHERE id = :id";
        $resultado = $conexion -> prepare($consulta);
        $resultado -> bindParam(":id", $datos["idPublicacion"]);

        try
        {
            $resultado -> execute();

            header($headerJSON);
            echo json_encode(["mensaje" => "Publicación Eliminada.", "error" => false]);
        }
        catch(PDOException $e)
        {
            header($headerJSON);
            echo json_encode(["mensaje" => "Error: " . $e, "error" => true]);
        }
    }
?>