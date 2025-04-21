<?php
require_once "utiles/funciones.php";
require_once "utiles/variables.php";



if($_SERVER["REQUEST_METHOD"] == "GET")
{
    $conexion = conectarPDO($host, $user, $password, $bbdd);

    $consulta = "SELECT * FROM publicaciones";

    $resultado = resultadoConsulta($conexion, $consulta);

    $publicaciones = [];

    while($publicacion = $resultado -> fetch(PDO::FETCH_ASSOC))
    {
        $publicaciones[] = $publicacion;
    }

    header($headerJSON);
    salidaDatos($publicaciones, $codigosHTTP[200]);
}



//En caso de que ninguna de las opciones anteriores se haya ejecutado
header ($headerJSON);
header ($codigosHTTP["400"]);
?>