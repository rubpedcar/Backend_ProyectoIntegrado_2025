<?php
// Incluye ficheros de variables y funciones
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);

error_reporting(E_ALL);

// Comprobamos si nos llega los datos por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //-----------------------------------------------------
    // Validaciones
    //-----------------------------------------------------

    $errores = [];
    $email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : "";
    $psswrd = isset($_REQUEST["password"]) ? $_REQUEST["password"] : "";

    // Email
    if (!validarEmail($email)) {
        $errores[] = "Campo Email no tiene un formato válido";
    }


    /* Verificar que no existe en la base de datos el mismo email */
    // Conecta con base de datos
    
    $conexion = conectarPDO($host, $user, $password, $bbdd);
    // Cuenta cuantos emails existen
    $select = "SELECT COUNT(*) as numero FROM usuarios WHERE email = :email";
    $consulta = $conexion->prepare($select);
    $consulta -> bindParam(":email", $email);
    // Ejecuta la búsqueda
    $consulta->execute();
    // Recoge los resultados
    $resultado = $consulta->fetch();
    $consulta = null;
    // Comprueba si existe
    if ($resultado["numero"] > 0) {
        $errores[] = "La dirección de email ya esta registrada.";
    }

    //-----------------------------------------------------
    // Crear cuenta
    //-----------------------------------------------------
    if (count($errores) === 0) {
        /* Registro En La Base De Datos */
        // Prepara INSERT
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $insert = "INSERT INTO usuarios (nombre, email, perfil_id, password, activo, token, created_at, updated_at) VALUES (:nombre, :email, :perfil, :password, :activo, :token, NOW(), NOW())";
        $consulta = $conexion->prepare($insert);

        // Ejecuta el nuevo registro en la base de datos
        $consulta->execute([
            "email" => $email,
            "password" => password_hash($psswrd, PASSWORD_BCRYPT),
            "activo" => 0,
            "token" => $token
        ]);
        $consulta = null;
        /* Envío De Email Con Token */
        // Cabecera
        $headers = [
            "From" => "dwes@php.com",
            "Content-type" => "text/plain; charset=utf-8"
        ];
        // Variables para el email
        $emailEncode = urlencode($email);
        $tokenEncode = urlencode($token);
        // Texto del email
        $textoEmail = "
    Hola!\n
    Gracias por registrate en la mejor plataforma de internet, demuestras inteligencia.\
    n
    Para activar entra en el siguiente enlace:\n
    http://localhost:3000/DWES/BBDD/empresa_pdo_roles/acceso/verificar-cuenta.php?email=$emailEncode&token=$tokenEncode
    ";
        // Envio del email
        mail($email, 'Activa tu cuenta', $textoEmail, $headers);
        /* Redirección a login.php con GET para informar del envío del email */
        header('Location: identificarse.php?registrado=1');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>

<body>
    <h1>Registro</h1>
    <!-- Mostramos errores por HTML -->
    <?php if (isset($errores)): ?>
        <ul class="errores">
            <?php
            foreach ($errores as $error) {
                echo '<li>' . $error . '</li>';
            }
            ?>
        </ul>
    <?php endif; ?>
    <!-- Formulario -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <p>
            <!-- Campo de Email -->
            <label>
                Correo electrónico
                <input type="text" name="email" required>
            </label>
        </p>
        <p>
            <!-- Campo de Contraseña -->
            <label>
                Contraseña
                <input type="password" name="password" required>
            </label>
        </p>
        <p>
            <!-- Botón submit -->
            <input type="submit" value="Registrarse">
        </p>
    </form>
</body>

</html>