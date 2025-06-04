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
        else
        {
            $consulta = "SELECT * FROM publicaciones";

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
        
    }
?>