<?php

session_start();

if($_SESSION['rol'] != 1){
    header("location:./"); 
}

include "../conexion.php";

if(!empty($_POST)){  

    $alert='';

    if(empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['correo']) || empty($_POST['usuario'])
        || empty($_POST['rol'])){

            $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
    }
    else{

        $idusuario  = $_POST['idusuario'];
        $nombre     = $_POST['nombre'];
        $apellido   = $_POST['apellido'];
        $correo     = $_POST['correo'];
        $usuario    = $_POST['usuario'];
        $clave      = $_POST['clave'];
        $rol        = $_POST['rol'];

        $query = mysqli_query($conection,"SELECT * FROM usuario 
                                          WHERE (usuario = '$usuario' AND  idusuario != $idusuario) 
                                          OR (correo = '$correo' AND idusuario != $idusuario)");
        $result = mysqli_fetch_array($query);

        if($result > 0){
            $alert='<p class="msg_error">El correo o el usuario no existen.</p>';
        }
        else{
            if(empty($_POST['clave'])){

                $sql_update = mysqli_query($conection, "UPDATE usuario
                                                        SET nombre = '$nombre', apellido = '$apellido', correo = '$correo',
                                                        usuario = '$usuario', rol = '$rol'
                                                        WHERE idusuario = $idusuario ");

            }
            else{
                $sql_update = mysqli_query($conection, "UPDATE usuario
                                                        SET nombre = '$nombre', apellido = '$apellido', correo = '$correo',
                                                        usuario = '$usuario', clave = '$clave', rol = '$rol'
                                                        WHERE idusuario = $idusuario ");
            }

            if($sql_update){
                $alert='<p class="msg_save">Usuario actualizado con exito.</p>';
            }
            else{
                $alert='<p class="msg_error">Error al actualizar el usuario.</p>';
            }
        }
    }
}

//Mostrar datos de los usuarios

if(empty($_GET['id'])){
        header('location: lista_usuario.php');
}

$iduser = $_GET['id'];

$sql=mysqli_query($conection,"SELECT u.idusuario, u.nombre, u.apellido, u.correo, u.usuario, (u.rol) as idrol, (r.rol) as rol
                              FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE idusuario = $iduser"); 

$result_sql = mysqli_num_rows($sql);

if($result_sql ==0){
    header('Location: lista_usuario.php');
}
else{
    while($data = mysqli_fetch_array($sql)){
        
        $idusuario  = $data['idusuario'];
        $nombre     = $data['nombre'];
        $apellido   = $data['apellido'];
        $correo     = $data['correo'];
        $usuario    = $data['usuario'];
        $idrol      = $data['idrol'];
        $rol      = $data['rol']; 

        if($idrol == 1){
            $option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
        }
        else if($idrol==2){
            $option = '<option value="'.$idrol.'"select>'.$rol.'</option>';
        }
        else if($idrol==3){
            $option = '<option value="'.$idrol.'"select>'.$rol.'</option>';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Editar usuario</title>
    
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="form_register">

            <h1>Editar usuario</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert: ''; ?></div>

            <form action="" method="post">
                <input type="hidden" name="idusuario" value="<?php echo $idusuario ?>">

                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?php echo $nombre ?>" >

                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" placeholder="Apellidos" value="<?php echo $apellido ?>">

                <label for="correo">Correo</label>
                <input type="email" name="correo" id="correo" placeholder="Correo electronico" value="<?php echo $correo ?>">

                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="Escriba un usuario" value="<?php echo $usuario ?>">

                <label for="clave">Clave</label>
                <input type="password" name="clave" id="clave" placeholder="Escriba una clave de acceso">

                <label for="rol">Tipo de rol</label>
                
                <?php
                    $query_rol = mysqli_query($conection,"SELECT * FROM rol");
                    $result_rol = mysqli_num_rows($query_rol);
  
                ?>
                
                <select name="rol" id="rol" class="notItemOne">
                    <?php
                        echo $option;
                        if($result_rol > 0){
                            while($rol = mysqli_fetch_array($query_rol)){
                    ?>
                        <option value="<?php echo $rol["idrol"]; ?>"><?php echo $rol["rol"] ?></option>
                    <?php
                            }
                        }                 
                    ?>
                </select>

                <input type="submit" value="Actualizar usuario" class="btn_save">
            </form>

        </div>
    

	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>