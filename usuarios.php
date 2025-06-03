<?php
    require_once "utiles/funciones.php";
    require_once "utiles/variables.php";

    header("Access-Control-Allow-Origin: http://localhost/hlc/tarde/ProyectoIntegrado_2025/");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");


    if($_SERVER["REQUEST_METHOD"] == "GET")
    {
        $conexion = conectarPDO($host, $user, $password, $bbdd);

        if(isset($_GET["id"]))
        {
            $consulta = "SELECT * FROM usuarios WHERE id = :id";

            $resultado = $conexion -> prepare($consulta);
            $resultado -> bindParam(":id", $_GET["id"]);

            $resultado -> execute();

            $usuario = $resultado -> fetch(PDO::FETCH_ASSOC);

            echo json_encode($usuario);
        }
        else
        {
            $consulta = "SELECT * FROM usuarios";

            $resultado = resultadoConsulta($conexion, $consulta);

            $usuarios = [];

            while($usuario = $resultado -> fetch(PDO::FETCH_ASSOC))
            {
                $usuarios[] = $usuario;
            }

            echo json_encode($usuarios);
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $datos = json_decode(file_get_contents("php://input"), true);

        $conexion = conectarPDO($host, $user, $password, $bbdd);

        $consulta = "SELECT * FROM usuarios WHERE email = :email";
        $resultado = $conexion -> prepare($consulta);
        $resultado -> bindParam(":email", $datos["formEmail"]);
        $resultado -> execute();

        if($resultado -> rowCount() == 0)
        {
            $consulta = "INSERT INTO usuarios (nombre, email, password, rol_id, activo, token, created_at, updated_at) VALUES (:nombre, :email, :password, 2, 1, '', NOW(), NOW())";

            $resultado = $conexion -> prepare($consulta);

            $resultado -> bindParam(":nombre", $datos["formNombre"]);
            $resultado -> bindParam(":email", $datos["formEmail"]);
            $resultado -> bindParam(":password", password_hash($datos["formPsswrd"], PASSWORD_BCRYPT));


            try
            {
                $resultado -> execute();

                header($headerJSON);
                echo json_encode(["mensaje" => "Usuario creado correctamente", "error" => false]);
                //echo $codigosHTTP["200"];
            }
            catch(PDOException $e)
            {
                header($headerJSON);
                echo json_encode(["mensaje" => "Error: ". $e -> getMessage(), "error" => true]);
                //echo $codigosHTTP["500"];
            }
        }
        else
        {
            header($headerJSON);
            echo json_encode(["mensaje" => "Error: Ya existe un usuario con ese email", "error" => true]);
            //echo $codigosHTTP["404"];
        }
    }


    if($_SERVER["REQUEST_METHOD"] == "PUT")
    {
        $datos = json_decode(file_get_contents("php://input"), true);

        $conexion = conectarPDO($host, $user, $password, $bbdd);

        $consulta = "SELECT * FROM usuarios WHERE nombre = :nombre";

        $resultado = $conexion -> prepare($consulta);

        $resultado -> bindParam(":nombre", $datos["nombre"]);

        $resultado -> execute();

        if($resultado -> rowCount() == 0)
        {
            $consulta = "SELECT * FROM usuarios WHERE nombre = :email";

            $resultado = $conexion -> prepare($consulta);

            $resultado -> bindParam(":email", $datos["email"]);

            $resultado -> execute();

            if($resultado -> rowCount() == 0)
            {
                header($headerJSON);
                echo json_encode(["mensaje" => "Error: Ya existe un usuario con ese email", "error" => false]);
            }
            else
            {
                header($headerJSON);
                echo json_encode(["mensaje" => "Error: Ya existe un usuario con ese email", "error" => true]);
            }
        }
        else
        {
            header($headerJSON);
            echo json_encode(["mensaje" => "Error: Ya existe un usuario con ese nombre", "error" => true]);
        }
    }
?>