<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrada</title>
</head>
<body>
    <a href="login-oculto.php" style="color: white;">.</a>
    <h1>Entrar</h1>     
    <!-- Formulario de identificación -->
    <form action="acceso.php" method="post">
        <p>
            <input type="text" name="email" placeholder="Email"> 
        </p> 
        <p>
            <input type="password" name="password" placeholder="Contraseña"> 
        </p>
        <p>
            <input type="submit" value="Entrar"> 
        </p>
        <p>
            <a href="identificarse.php">He olvidado mi contraseña.</a>
            <a href="registro.php">¿No tienes una cuenta? Regístrate aquí.</a>
        </p>
    </form>
</body>
</html>