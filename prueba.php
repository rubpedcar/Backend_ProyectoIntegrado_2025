<?php

require_once "utiles/funciones.php";
require_once "utiles/variables.php";

$conexion = conectarPDO($host, $user, $password, $bbdd);

$consulta = "SELECT * FROM categorias";

$resultado = resultadoConsulta($conexion, $consulta);

while($registro = $resultado -> fetch(PDO::FETCH_ASSOC))
{
    echo "<pre>";
    print_r($registro);
    echo "</pre>";
}


?>