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

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $datos = json_decode(file_get_contents("php://input"), true);

    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $consulta = "SELECT * FROM bases";
    $resultado = resultadoConsulta($conexion, $consulta);
    $bases  = $resultado -> fetch(PDO::FETCH_ASSOC);
    $maxVotos = $bases["limite_votos"];

    $consulta = "SELECT * FROM votaciones WHERE usuario_ip = :ip";
    $resultado = $conexion -> prepare($consulta);
    $resultado -> bindParam(":ip", $datos["usuario_ip"]);
    $resultado -> execute();

    if($resultado -> rowCount() < $maxVotos)
    {
        $consulta = "INSERT INTO votaciones (publicacion_id, usuario_ip) VALUES (:publicacion_id, :usuario_ip)";
        $resultado = $conexion -> prepare($consulta);
        $resultado -> bindParam(":publicacion_id", $datos["publicacion_id"]);
        $resultado -> bindParam(":usuario_ip", $datos["usuario_ip"]);
        

        try
        {
            $resultado -> execute();

            header($headerJSON);
            echo json_encode(["error" => false, "mensaje" => "Voto registrado correctamente."]);
        }
        catch(PDOException $e)
        {
            header($headerJSON);
            echo json_encode(["error" => true, "mensaje" => "Error al registrar el voto: " . $e->getMessage()]);
        }
        
    }
    else
    {
        header($headerJSON);
        echo json_encode(["error" => true, "mensaje" => "Ya has alcanzado el límite de votos."]);
    }
}
?>