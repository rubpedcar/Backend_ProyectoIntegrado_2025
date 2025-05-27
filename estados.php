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
?>