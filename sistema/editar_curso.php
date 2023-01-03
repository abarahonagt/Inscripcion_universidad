<?php

session_start();
include "../conexion.php";

if(!empty($_POST)){

    $alert='';

    if(empty($_POST['descripcion']) || empty($_POST['facultad']) || empty($_POST['catedratico']) || empty($_POST['horario']) || empty($_POST['precio'])){

        $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
    }
    else{

        $codigo         = $_POST['id'];
        $descripcion    = $_POST['descripcion'];
        $facultad       = $_POST['facultad'];
        $catedratico    = $_POST['catedratico'];
        $horario        = $_POST['horario'];
        $precio        = $_POST['precio'];
        $usuario        = $_SESSION['idusuario'];

        $result = 0;

        $query = mysqli_query($conection,"SELECT * FROM curso WHERE (horario = '$horario' AND catedratico = '$catedratico')");
        $result = mysqli_fetch_array($query);
        //$result = count($result);
        

        // $sql_update = mysqli_query($conection, "UPDATE catedratico
        //                                         SET nombre = '$nombre', apellido = '$apellido', direccion = '$direccion',
        //                                             dpi = $dpi, telefono = $telefono
        //                                         WHERE codigo_cat = $codigo ");

        $sql_update = mysqli_query($conection, "UPDATE curso
                                                  SET descripcion = '$descripcion', facultad = '$facultad', catedratico = '$catedratico',
                                                      horario = '$horario', precio = '$precio',idusuario = '$usuario'
                                                  WHERE codigo_cur = '$codigo'");

             if($sql_update){
                $alert='<p class="msg_save">Catedratico actualizado con exito.</p>';
            }
            else{
                $alert='<p class="msg_error">Error al actualizar al Catedratico.</p>';
            }
        
    }
}

//Mostrar datos de los cursos

if(empty($_REQUEST['id'])){
        header('location: lista_curso.php');
}

$codigo = $_REQUEST['id'];

$sql=mysqli_query($conection,"SELECT * FROM curso WHERE codigo_cur = $codigo"); 

$result_sql = mysqli_num_rows($sql);

if($result_sql ==0){
    header('Location: lista_curso.php');
}
else{
    while($data = mysqli_fetch_array($sql)){
        
        $codigo         = $data['codigo_cur'];
        $descripcion    = $data['descripcion'];
        $facultad       = $data['facultad'];
        $catedratico    = $data['catedratico'];
        $horario        = $data['horario'];
        $precio         = $data['precio'];
        
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Editar Curso</title>
    
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="form_register">

            <h1>Editar Curso</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert: ''; ?></div>

            <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $codigo; ?>">
            
            <label for="descripcion">Descripcion del curso</label>
                <input type="text" name="descripcion" id="descripcion" placeholder="Ejemplo: Desarrollo Web I" value="<?php echo $descripcion;?>">

                <label for="facultad">Facultad</label>
                <?php
                    $query_facultad = mysqli_query($conection,"SELECT * FROM facultad");
                    $result_facultad = mysqli_num_rows($query_facultad);
  
                ?>
                <select name="facultad" id="facultad">
                    <?php

                        if($result_facultad > 0){
                            while($facultad = mysqli_fetch_array($query_facultad)){
                    ?>
                        <option value="<?php echo $facultad["idfacultad"]; ?>"><?php echo $facultad["facultad"] ?></option>
                    <?php
                            }
                        }                 
                    ?>
                </select>

                <label for="catedratico">Catedratico</label>
                <?php
                    $query_cat = mysqli_query($conection,"SELECT * FROM catedratico");
                    $result_cat = mysqli_num_rows($query_cat);
  
                ?>

                <select name="catedratico" id="catedratico">
                    <?php
                        if($result_cat > 0){
                            while($catedratico = mysqli_fetch_array($query_cat)){
                    ?>
                        <option value="<?php echo $catedratico["codigo_cat"]; ?>"><?php echo $catedratico["nombre"] ?> <?php echo $catedratico["apellido"] ?></option>
                    <?php
                            }
                        }                 
                    ?>                    
                </select>

                <label for="horario">Horario</label>
                <?php
                    $query_hora = mysqli_query($conection,"SELECT * FROM horario");
                    $result_hora = mysqli_num_rows($query_hora);
  
                ?>

                <select name="horario" id="horario">
                    <?php
                        if($result_hora > 0){
                            while($horario = mysqli_fetch_array($query_hora)){
                    ?>
                        <option value="<?php echo $horario["idhorario"]; ?>"><?php echo $horario["horario"] ?></option>
                    <?php
                            }
                        }                 
                    ?>   
                </select>

                <label for="precio">Precio del curso</label>
                <input type="text" name="precio" id="precio" placeholder="Q. 0.00" value="<?php echo $precio;?>">

                <input type="submit" value="Editar curso" class="btn_save">
            </form>

        </div>
    

	</section>
</body>
<?php include "includes/footer.php"; ?>
</html> 