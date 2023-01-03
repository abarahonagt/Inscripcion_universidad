<?php

session_start();

if($_SESSION['rol'] != 1){
    header("location:./"); 
}

include "../conexion.php";

    if(!empty($_POST)){
        
        $carnet = $_POST['carnet'];

        //$query_delete = mysqli_query($conection, "DELETE FROM  usuario WHERE idusuario = $idusuario");
        $query_delete = mysqli_query($conection, "UPDATE alumno SET  estado=0 WHERE carnet = $carnet");

        if($query_delete){
            header("location: lista_alumno.php");
        }
        else{
            echo "Ha ocurrido un error al intentar eliminar al Alumno,";
        }
    }

    if(empty($_REQUEST['id'])){
        header("location: lista_alumno.php");
    }
    else{

        $idalumno = $_REQUEST['id'];

        $query = mysqli_query($conection, "SELECT *
                                           FROM alumno
                                           WHERE carnet = $idalumno" );

        $result = mysqli_num_rows($query);

        if($result > 0){
            while($data = mysqli_fetch_array($query)){

                $carnet     = $data['carnet'];
                $nombre     = $data['nombre'];
                $apellido   = $data['apellido'];
                $direccion  = $data['direccion'];
                $dpi        = $data['dpi'];
                $telefono   = $data['telefono'];
            }
        }
        else{
            header("location: lista_alumno.php");
        }
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Eliminar Alumno</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<h1>Eliminar Alumno</h1>
        <div class="data_delete">
            <h2>¿Esta seguro de eliminar el siguiente alumno?</h2>
            <p>Nombre: <span><?php echo $nombre; ?></span></p>
            <p>Apellido: <span><?php echo $apellido; ?></span></p>
            <p>Direccion: <span><?php echo $direccion; ?></span></p>
            <p>DPI: <span><?php echo $dpi; ?></span></p>
            <p>Telefono: <span><?php echo $telefono; ?></span></p>

            <form method="POST" action="">
                <input type="hidden" name="carnet" value="<?php echo $carnet; ?>">
                <a href="lista_alumno.php" class="btn_cancel">Cancelar</a>
                <input type="submit" value="Aceptar" class="btn_ok">
            </form>

        </div>
	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>