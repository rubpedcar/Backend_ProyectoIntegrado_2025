<?php
require_once "utiles/funciones.php";
require_once "utiles/variables.php";

header("Access-Control-Allow-Origin: http://localhost/hlc/tarde/ProyectoIntegrado_2025/");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if($_SERVER["REQUEST_METHOD"] == "GET")
{
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    if(isset($_GET["ip"]))
    {
        $consulta = "SELECT * FROM votaciones WHERE usuario_ip = :ip";
        $resultado = $conexion -> prepare($consulta);
        $resultado -> bindParam(":ip", $_GET["ip"]);
        $resultado -> execute();

        $votacion = $resultado -> fetch(PDO::FETCH_ASSOC);

        echo json_encode($votacion);
    }
    else
    {
        $consulta = "SELECT * FROM votaciones";

        $resultado = resultadoConsulta($conexion, $consulta);

        $votaciones = [];

        while($votacion = $resultado -> fetch(PDO::FETCH_ASSOC))
        {
            $votaciones[] = $votacion;
        }

        echo json_encode($votaciones);
    }
}
?>