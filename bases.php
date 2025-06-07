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

    $bases = $resultado -> fetch(PDO::FETCH_ASSOC);

    echo json_encode($bases);
}

if($_SERVER["REQUEST_METHOD"] == "PUT")
{
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $datos = json_decode(file_get_contents("php://input"), true);

    $consulta = "UPDATE bases 
    SET limite_fotos = :limite_fotos, limite_votos = :limite_votos, plazo_votaciones = :plazo_votaciones, plazo_publicaciones = :plazo_publicaciones";

    $resultado = $conexion -> prepare($consulta);
    $resultado -> bindParam(":limite_fotos", $datos["limite_fotos"]);
    $resultado -> bindParam(":limite_votos", $datos["limite_votos"]);
    $resultado -> bindParam(":plazo_votaciones", $datos["plazo_votaciones"]);
    $resultado -> bindParam(":plazo_publicaciones", $datos["plazo_publicaciones"]);

    try
    {
        $resultado -> execute();

        header($headerJSON);
        echo json_encode(["error" => false, "mensaje" => "Bases actualizadas correctamente."]);
    }
    catch(PDOException $e)
    {
        header($headerJSON);
        echo json_encode(["error" => true, "mensaje" => "Error al actualizar las bases: " . $e->getMessage()]);
    }
}
?>