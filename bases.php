<?php
require_once "utiles/funciones.php";
require_once "utiles/variables.php";

header("Access-Control-Allow-Origin: http://localhost/hlc/tarde/ProyectoIntegrado_2025/");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if($_SERVER["REQUEST_METHOD"] == "GET")
{
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $consulta = "SELECT * FROM bases";

    $resultado = resultadoConsulta($conexion, $consulta);

    $bases = [];

    while($base = $resultado -> fetch(PDO::FETCH_ASSOC))
    {
        $bases[] = $base;
    }

    echo json_encode($bases);
}
?>