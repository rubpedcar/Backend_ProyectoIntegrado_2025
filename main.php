<?php
    require_once("utiles/funciones.php");
    require_once("utiles/variables.php");

    // Activa las sesiones
    session_name("sesion-privada");
    session_start();

    $perfil;

    if (isset($_SESSION["perfil"]))
    {
        $perfil = $_SESSION["perfil"];
    }
    else
    {
        $perfil = 0;
    }

    //print_r($_SESSION);
    

    $conexion =  conectarPDO($host, $user, $password, $bbdd);

    $select = "SELECT o.id as oferta_id, c.categoria, o.nombre, o.descripcion, o.fecha_actividad as fecha, o.aforo
                FROM ofertas o, categorias c
                WHERE o.categoria_id = c.id
                AND o.visada = 1";

    $resultado = resultadoConsulta($conexion, $select);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresa</title>
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
</head>

<nav>
    <?php if($perfil == 0) :?>
        <a href="acceso/login.php">Iniciar Sesión</a>
    <?php else :?>
        <p><?php echo($_SESSION["nombre"]);?></p>
        <a href="acceso/cerrar-sesion.php">Cerrar Sesión</a>
    <?php endif;?>
</nav>

<h1>Actividades</h1>
<body>
    <table border="1" cellpadding="10">
        <thead>
            <th>Categoría</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Aforo</th> 
            
            <?php if($perfil == 4): ?>
                <th></th>
            <?php endif; ?>
            
        </thead>
        <tbody>

            <!-- Muestra los datos -->
            <?php
                while ($registro = $resultado->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td>"
                        . $registro["categoria"] . "</td><td>"
                        . $registro["nombre"] . "</td><td>"
                        . $registro["descripcion"] . "</td><td>"
                        . $registro["fecha"] . "</td><td>"
                        . $registro["aforo"] . "</td>";


                    if($perfil == 4)
                    {
                        $consulta = "SELECT *
                                        FROM solicitudes 
                                        WHERE oferta_id = :oferta_id
                                        AND usuario_id = :usuario_id";
                        
                        $resultado2 = $conexion -> prepare($consulta);
                        $resultado2 -> bindParam(":oferta_id", $registro["oferta_id"]);
                        $resultado2 -> bindParam(":usuario_id", $_SESSION["id"]);


                        $resultado2 -> execute();
                        

                        // Si el usuario ya está apuntado, se le da la opción de borrarse de la actividad.
                        if($resultado2 -> rowCount() > 0)
                        {
                            echo "<td><a href='solicitudes/borrarse.php?oferta_id=" . $registro["oferta_id"] . "' class='estilo_enlace'</a>Borrarse</td>";
                        }
                        else
                        {
                            echo "<td><a href='solicitudes/apuntarse.php?oferta_id=" . $registro["oferta_id"] . "' class='estilo_enlace'</a>Apuntarse</td>";
                        }

                        
                    }
                    echo "</tr>";
                }
            ?>

        </tbody>
    </table>

    <?php if($perfil == 3):?>
        <button><a href="ofertas/nuevo.php">Crear una nueva actividad</a></button>

        <h1>Tus actividades</h1>

        <table border="1" cellpadding="10">
            <thead>
                <th>Categoría</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Aforo</th> 
                <th></th>
                
            </thead>
            <tbody>

                <!-- Muestra los datos -->
                <?php
                    $conexion = conectarPDO($host, $user, $password, $bbdd);

                    $consulta = "SELECT o.id as oferta_id, c.categoria, o.nombre, o.descripcion, o.fecha_actividad as fecha, o.aforo
                                    FROM ofertas o, categorias c
                                    WHERE o.categoria_id = c.id
                                    AND o.usuario_id = :usuario_id
                                    AND o.visada = 0";

                    $resultado = $conexion -> prepare($consulta);
                    $resultado -> bindParam(":usuario_id", $_SESSION["id"]);

                    $resultado -> execute();


                    while ($registro = $resultado->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr><td>"
                            . $registro["categoria"] . "</td><td>"
                            . $registro["nombre"] . "</td><td>"
                            . $registro["descripcion"] . "</td><td>"
                            . $registro["fecha"] . "</td><td>"
                            . $registro["aforo"] . "</td>";

                            echo "<td><a href='ofertas/modificar.php?oferta_id=" . $registro["oferta_id"] . "' class='estilo_enlace'</a>&#9998
                            <a href='ofertas/borrar.php?oferta_id=" . $registro["oferta_id"] . "' class='confirmacion_borrar'</a>&#128465</td>";

                        echo "</tr>";
                    }
                ?>

            </tbody>
        </table>
    <?php endif;?>



    <?php if($perfil == 2):?>
        <h1>Actividades por visar</h1>

        <table border="1" cellpadding="10">
            <thead>
                <th>Categoría</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Aforo</th> 
                <th></th>
                
            </thead>
            <tbody>

                <!-- Muestra los datos -->
                <?php
                    $conexion = conectarPDO($host, $user, $password, $bbdd);

                    $consulta = "SELECT o.id as oferta_id, c.categoria, o.nombre, o.descripcion, o.fecha_actividad as fecha, o.aforo
                                    FROM ofertas o, categorias c
                                    WHERE o.categoria_id = c.id
                                    AND o.visada = 0";

                    $resultado = resultadoConsulta($conexion, $consulta);
                    $resultado -> execute();


                    while ($registro = $resultado->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr><td>"
                            . $registro["categoria"] . "</td><td>"
                            . $registro["nombre"] . "</td><td>"
                            . $registro["descripcion"] . "</td><td>"
                            . $registro["fecha"] . "</td><td>"
                            . $registro["aforo"] . "</td>";

                            echo "<td><a href='ofertas/visar.php?oferta_id=" . $registro["oferta_id"] . "' class='estilo_enlace'</a>Visar</td>";

                        echo "</tr>";
                    }
                ?>

            </tbody>
        </table>
    <?php endif;?>



    <script type="text/javascript">
        var elementos = document.getElementsByClassName("confirmacion_borrar");
        var confirmFunc = function (e)
        {
            if (!confirm('Está seguro de que desea borrar este registro?'))
            e.preventDefault();
        };
            
        for (var i = 0, l = elementos.length; i < l; i++) 
        {
            elementos[i].addEventListener('click', confirmFunc, false);
        }
    </script>
</body>
</html>

</html>