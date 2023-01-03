<?php

session_start();

include "../conexion.php";

if(!empty($_POST)){

    $alert=''; 

    if(empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['direccion']) || empty($_POST['dpi'])
        || empty($_POST['telefono'])){

            $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
    }
    else{


        $nombre     = $_POST['nombre'];
        $apellido   = $_POST['apellido'];
        $direccion  = $_POST['direccion'];
        $dpi        = $_POST['dpi'];
        $telefono   = $_POST['telefono'];
        $idusuario  = $_SESSION['idusuario'];

        //validacion de que DPI no este repetido

        $result = 0;

        if(is_numeric($dpi)){
            $query = mysqli_query($conection,"SELECT * FROM catedratico WHERE dpi = '$dpi'");
            $result = mysqli_fetch_array($query);
        }

        if($result > 0){
            $alert='<p class="msg_error">El numero de DPI ya existe.</p>';
        }
        else{
            $query_insert=mysqli_query($conection, "INSERT INTO catedratico(nombre,apellido,direccion,dpi,telefono,idusuario)
                                                    VALUES('$nombre','$apellido','$direccion','$dpi','$telefono','$idusuario')");

            if($query_insert){
                $alert='<p class="msg_save">Catedratico creado con exito.</p>';
            }
            else{
                $alert='<p class="msg_error">Error al crear el Catedratico.</p>';
            }
        }       
    }
    //mysqli_close($conection);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Registro de Catedraticos</title>
    
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="form_register">

            <h1>Registro de Catedraticos</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert: ''; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo">

                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" placeholder="Apellidos">

                <label for="direccion">Direccion</label>
                <input type="text" name="direccion" id="direccion" placeholder="Direccion completa">

                <label for="dpi">DPI</label>
                <input type="text" name="dpi" id="dpi" placeholder="Documento de indentificacion">

                <label for="telefono">Telefono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Numero de telefono">

                <input type="submit" value="Guardar Catedratico" class="btn_save">
            </form>

        </div>
    

	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>