<?php

session_start();

if($_SESSION['rol'] != 1){
    header("location:./"); 
}

include "../conexion.php";

    if(!empty($_POST)){
        
        $codigo = $_POST['codigo_cur'];

        //$query_delete = mysqli_query($conection, "DELETE FROM  usuario WHERE idusuario = $idusuario");
        $query_delete = mysqli_query($conection, "UPDATE curso SET  estado=0 WHERE codigo_cur = $codigo");

        if($query_delete){
            header("location: lista_curso.php");
        }
        else{
            echo "Ha ocurrido un error al intentar eliminar el Curso,";
        }
    }

    if(empty($_REQUEST['id'])){
        header("location: lista_curso.php");
    } 
    else{

        $codigo = $_REQUEST['id'];

        $query = mysqli_query($conection, "SELECT cu.codigo_cur, cu.descripcion, f.facultad, ca.nombre, ca.apellido, h.horario, cu.precio
                                                FROM curso cu 
                                                INNER JOIN facultad f ON cu.facultad = f.idfacultad
                                                INNER JOIN catedratico ca ON cu.catedratico = ca.codigo_cat
                                                INNER JOIN horario h ON cu.horario = h.idhorario
                                                WHERE cu.codigo_cur = $codigo ");

        $result = mysqli_num_rows($query);

        if($result > 0){
            while($data = mysqli_fetch_array($query)){
 
                $codigo     = $data['codigo_cur'];
                $descripcion     = $data['descripcion'];
                $facultad   = $data['facultad'];
                $nombre  = $data['nombre'];
                $apellido  = $data['apellido'];
                $horario        = $data['horario'];
                $precio        = $data['precio'];
                
            }
        }
        else{
            header("location: lista_curso.php");
        }
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Eliminar Curso</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<h1>Eliminar Curso</h1>
        <div class="data_delete">
            <h2>¿Esta seguro de eliminar el siguiente curso?</h2>
            <p>Descripcion: <span><?php echo $descripcion; ?></span></p>
            <p>Facultad: <span><?php echo $facultad; ?></span></p>
            <p>Catedratico: <span><?php echo $nombre; ?> <?php echo $apellido; ?></span></p>
            <p>Horario: <span><?php echo $horario; ?></span></p>
            <p>Precio: <span><?php echo $precio; ?></span></p>

            <form method="POST" action="">
                <input type="hidden" name="codigo_cur" value="<?php echo $codigo; ?>">
                <a href="lista_curso.php" class="btn_cancel">Cancelar</a>
                <input type="submit" value="Aceptar" class="btn_ok">
            </form>

        </div>
	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>