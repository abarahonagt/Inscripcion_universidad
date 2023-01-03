<?php

session_start();

if($_SESSION['rol'] != 1){
    header("location:./"); 
}

include "../conexion.php";

    if(!empty($_POST)){
        
        $codigo = $_POST['codigo_cat'];

        //$query_delete = mysqli_query($conection, "DELETE FROM  usuario WHERE idusuario = $idusuario");
        $query_delete = mysqli_query($conection, "UPDATE catedratico SET  estado=0 WHERE codigo_cat = $codigo");

        if($query_delete){
            header("location: lista_catedratico.php");
        }
        else{
            echo "Ha ocurrido un error al intentar eliminar al Catedratico,";
        }
    }

    if(empty($_REQUEST['id'])){
        header("location: lista_catedratico.php");
    }
    else{

        $idcatedratico = $_REQUEST['id'];

        $query = mysqli_query($conection, "SELECT *
                                           FROM catedratico
                                           WHERE codigo_cat = $idcatedratico" );

        $result = mysqli_num_rows($query);

        if($result > 0){
            while($data = mysqli_fetch_array($query)){

                $codigo     = $data['codigo_cat'];
                $nombre     = $data['nombre'];
                $apellido   = $data['apellido'];
                $direccion  = $data['direccion'];
                $dpi        = $data['dpi'];
                $telefono   = $data['telefono'];
            }
        }
        else{
            header("location: lista_catedratico.php");
        }
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Eliminar Catedratico</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<h1>Eliminar Catedratico</h1>
        <div class="data_delete">
            <h2>¿Esta seguro de eliminar el siguiente catedratico?</h2>
            <p>Nombre: <span><?php echo $nombre; ?></span></p>
            <p>Apellido: <span><?php echo $apellido; ?></span></p>
            <p>Direccion: <span><?php echo $direccion; ?></span></p>
            <p>DPI: <span><?php echo $dpi; ?></span></p>
            <p>Telefono: <span><?php echo $telefono; ?></span></p>

            <form method="POST" action="">
                <input type="hidden" name="codigo_cat" value="<?php echo $codigo; ?>">
                <a href="lista_catedratico.php" class="btn_cancel">Cancelar</a>
                <input type="submit" value="Aceptar" class="btn_ok">
            </form>

        </div>
	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>