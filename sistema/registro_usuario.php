<?php

//validacion de acceso por tipo de rol 1= admin 2=estudiante 3=catedratico
session_start();

if($_SESSION['rol'] != 1){
    header("location:./"); 
}

include "../conexion.php";

if(!empty($_POST)){

    $alert='';

    if(empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['correo']) || empty($_POST['usuario'])
        || empty($_POST['clave']) || empty($_POST['rol'])){

            $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
    }
    else{


        $nombre     = $_POST['nombre'];
        $apellido   = $_POST['apellido'];
        $correo     = $_POST['correo'];
        $usuario    = $_POST['usuario'];
        $clave      = md5($_POST['clave']); 
        $rol        = $_POST['rol'];

        $query = mysqli_query($conection,"SELECT * FROM usuario WHERE usuario = '$usuario' OR correo = '$correo'");
        $result = mysqli_fetch_array($query);

        if($result > 0){
            $alert='<p class="msg_error">El correo o el usuario no existen.</p>';
        }
        else{
            $query_insert=mysqli_query($conection, "INSERT INTO usuario(nombre,apellido,correo,usuario,clave,rol)
                                                    VALUES('$nombre','$apellido','$correo','$usuario','$clave','$rol')");

            if($query_insert){
                $alert='<p class="msg_save">Usuario creado con exito.</p>';
            }
            else{
                $alert='<p class="msg_error">Error al crear el usuario.</p>';
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Registro de usuarios</title>
    
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="form_register">

            <h1>Registro de usuarios</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert: ''; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo">

                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" placeholder="Apellidos">

                <label for="correo">Correo</label>
                <input type="email" name="correo" id="correo" placeholder="Correo electronico">

                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="Escriba un usuario">

                <label for="clave">Clave</label>
                <input type="password" name="clave" id="clave" placeholder="Escriba una clave de acceso">

                <label for="rol">Tipo de rol</label>
                
                <?php
                    $query_rol = mysqli_query($conection,"SELECT * FROM rol");
                    $result_rol = mysqli_num_rows($query_rol);
  
                ?>
                
                <select name="rol" id="rol">
                    <?php
                        if($result_rol > 0){
                            while($rol = mysqli_fetch_array($query_rol)){
                    ?>
                        <option value="<?php echo $rol["idrol"]; ?>"><?php echo $rol["rol"] ?></option>
                    <?php
                            }
                        }                 
                    ?>
                </select>

                <input type="submit" value="Crear usuario" class="btn_save">
            </form>

        </div>
    

	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>