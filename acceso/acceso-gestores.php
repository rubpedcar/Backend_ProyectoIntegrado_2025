<?php 
// Incluye ficheros de variables y funciones
require_once("../utiles/variables.php");
require_once("../utiles/funciones.php");

// Comprobamos que nos llega los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{

    $emailFormulario = isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
    $psswdFormulario = isset($_REQUEST['password']) ? $_REQUEST['password'] : null;

    $psswd = "";
    $perfil = "";
    $email = "";
    $id = "";

    $flag = false;
    

    $conexion =  conectarPDO($host, $user, $password, $bbdd);

    $consulta = "SELECT * FROM gestores WHERE email = :email";

        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(":email", $emailFormulario);

        $resultado->execute();

        if ($resultado->rowCount() != 0) 
        {
            $registro = $resultado->fetch(PDO::FETCH_ASSOC);

            $id = $registro["id"];
            $email = $registro["email"];
            $nombre = $registro["nombre"];
            $psswd = $registro["password"];
            $perfil = $registro["perfil_id"];

            $flag = true;
        } 


    // Base de datos ficticia que se usará en el ejemplo.
    // $baseDeDatos = [ 'email' => 'correo@ejemplo.com', 'password' => password_hash('123', PASSWORD_BCRYPT) ];
    // Variables del formulario

    //echo "<p>Email: " . $emailFormulario ."</p>". PHP_EOL;
    //echo "<p>Password: " . $contrasenaFormulario ."</p>". PHP_EOL;


    // Comprobamos si los datos son correctos
    if($email == $emailFormulario && password_verify($psswdFormulario, $psswd))
    {
        if ($flag) 
        {
            // Si son correctos, creamos la sesión
            session_name("sesion-privada");
            session_start();
            $_SESSION['email'] = $email;
            $_SESSION["nombre"] = $nombre;
            $_SESSION["perfil"] = $perfil;
            $_SESSION["id"] = $id;
            // Redireccionamos a la página privada
            //echo "Vamos a privado";
            header('Location: ../index.php');
            exit();
        }
    }
    else
    {
        print'<p style="color: red">El email o la contraseña es incorrecta.</p>';
        header("refresh:3;url=login.php");
        exit();
    }
}
?>