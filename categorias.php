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
            $consulta = "SELECT categoria FROM categorias WHERE id = :id";

            $resultado = $conexion -> prepare($consulta);
            $resultado -> bindParam(":id", $_GET["id"]);

            $resultado -> execute();

            $nombre = $resultado -> fetch(PDO::FETCH_ASSOC);

            echo json_encode($nombre);
        }
        else
        {
            $consulta = "SELECT * FROM categorias";

            $resultado = resultadoConsulta($conexion, $consulta);

            $categorias = [];

            while($categoria = $resultado -> fetch(PDO::FETCH_ASSOC))
            {
                $categorias[] = $categoria;
            }

            echo json_encode($categorias);
        }
    }

    
?>