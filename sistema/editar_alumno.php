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
 
        $carnet     = $_POST['id'];
        $nombre     = $_POST['nombre'];
        $apellido   = $_POST['apellido'];
        $direccion  = $_POST['direccion'];
        $dpi        = $_POST['dpi'];
        $telefono   = $_POST['telefono'];

        //validacion DPI repetido

        if(is_numeric($dpi) and $dpi != 0){
            $query = mysqli_query($conection,"SELECT * FROM alumno WHERE (dpi = $dpi AND carnet != $carnet)");

            $result = mysqli_fetch_array($query);
            //$result = count($result);
        }

        if($result > 0){
            $alert='<p class="msg_error">El DPI ya existe.</p>';
        }
        else{

                $sql_update = mysqli_query($conection, "UPDATE alumno
                                                        SET nombre = '$nombre', apellido = '$apellido', direccion = '$direccion',
                                                        dpi = $dpi, telefono = $telefono
                                                        WHERE carnet = $carnet ");

             if($sql_update){
                $alert='<p class="msg_save">Alumno actualizado con exito.</p>';
            }
            else{
                $alert='<p class="msg_error">Error al actualizar al Alumno.</p>';
            }
        }
    }
}

//Mostrar datos de los alumnos

if(empty($_REQUEST['id'])){
        header('location: lista_alumno.php');
}

$carnet = $_REQUEST['id'];

$sql=mysqli_query($conection,"SELECT * FROM alumno WHERE carnet = $carnet"); 

$result_sql = mysqli_num_rows($sql);

if($result_sql ==0){
    header('Location: lista_alumno.php');
}
else{
    while($data = mysqli_fetch_array($sql)){
        
        $carnet     = $data['carnet'];
        $nombre     = $data['nombre'];
        $apellido   = $data['apellido'];
        $direccion  = $data['direccion'];
        $dpi        = $data['dpi'];
        $telefono   = $data['telefono'];


    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Editar usuario</title>
    
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="form_register">

            <h1>Editar Alumno</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert: ''; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $carnet; ?>">

                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value="<?php echo $nombre;?>">

                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" placeholder="Apellidos" value="<?php echo $apellido;?>">

                <label for="direccion">Direccion</label>
                <input type="text" name="direccion" id="direccion" placeholder="Direccion completa" value="<?php echo $direccion;?>">

                <label for="dpi">DPI</label>
                <input type="text" name="dpi" id="dpi" placeholder="Documento de indentificacion" value="<?php echo $dpi;?>">

                <label for="telefono">Telefono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Numero de telefono" value="<?php echo $telefono;?>">

                <input type="submit" value="Guardar Alumno" class="btn_save">
            </form>

        </div>
    

	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>