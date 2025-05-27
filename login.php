<?php
// Incluye ficheros de variables y funciones
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

header("Access-Control-Allow-Origin: http://localhost/hlc/tarde/ProyectoIntegrado_2025/");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Comprobamos que nos llega los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true);

    $emailFormulario = $datos["formEmail"];
    $psswdFormulario = $datos["formPsswrd"];

    $psswd = "";
    $rol = "";
    $email = "";
    $id = "";


    $conexion =  conectarPDO($host, $user, $password, $bbdd);

    $consulta = "SELECT * FROM usuarios WHERE email = :email";

    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(":email", $emailFormulario);

    $resultado->execute();

    if ($resultado->rowCount() != 0) {
        $registro = $resultado->fetch(PDO::FETCH_ASSOC);

        $id = $registro["id"];
        $email = $registro["email"];
        $nombre = $registro["nombre"];
        $psswd = $registro["password"];
        $rol = $registro["rol_id"];
    }


    // Comprobamos si los datos son correctos
    if ($email == $emailFormulario && password_verify($psswdFormulario, $psswd)) {
        if ($registro["activo"] == 1) {
            // Si son correctos, creamos la sesión
            session_name("sesion-privada");
            session_start();
            $_SESSION['email'] = $email;
            $_SESSION["nombre"] = $nombre;
            $_SESSION["rol"] = $rol;
            $_SESSION["id"] = $id;

            // Mandamos los datos de vuelta.
            $datos = [];
            $datos["id"] = $email;
            $datos["nombre"] = $nombre;
            $datos["email"] = $email;
            $datos["rol"] = $rol;
            $datos["mensaje"] = "OK";
            $datos["error"] = false;
            echo json_encode($datos);
            exit();
        } else {
            $datos = [];
            $datos["mensaje"] = "Debes activar tu cuenta primero.";
            $datos["error"] = true;
            echo json_encode($datos);
            exit();
        }
    } else {
        $datos = [];
        $datos["mensaje"] = "El email o la contraseña es incorrecta.";
        $datos["error"] = true;
        echo json_encode($datos);
        exit();
    }
}
