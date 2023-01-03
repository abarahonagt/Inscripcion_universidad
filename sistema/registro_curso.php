<?php

session_start();

include "../conexion.php";

if(!empty($_POST)){

    $alert='';

    if(empty($_POST['descripcion']) || empty($_POST['facultad']) || empty($_POST['catedratico']) || empty($_POST['horario']) || empty($_POST['precio'])){

            $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
    }
    else{


        $descripcion     = $_POST['descripcion'];
        $facultad        = $_POST['facultad'];
        $catedratico    = $_POST['catedratico'];
        $horario         = $_POST['horario'];
        $precio         = $_POST['precio'];
        $usuario         = $_SESSION['idusuario'];

        $result = 0;

        $query = mysqli_query($conection,"SELECT * FROM curso WHERE horario = '$horario' AND catedratico = '$catedratico'");
        $result = mysqli_fetch_array($query);

        if($result > 0){
            $alert='<p class="msg_error">El catedratico ya posee un curso asignado en ese horario</p>';
        }  
        else{

            $query_insert=mysqli_query($conection, "INSERT INTO curso(descripcion,facultad,catedratico,horario,precio,idusuario)
                                                    VALUES('$descripcion','$facultad','$catedratico','$horario','$precio','$usuario')");

            if($query_insert){
                $alert='<p class="msg_save">Curso creado con exito.</p>';
            }
            else{
                $alert='<p class="msg_error">Error al crear el Curso.</p>';
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
	<title>Registro de Cursos</title>
    
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="form_register">

            <h1>Registro de Cursos</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert: ''; ?></div>

            <form action="" method="post" enctype="multipart/form-data">

                <label for="descripcion">Descripcion del curso</label>
                <input type="text" name="descripcion" id="descripcion" placeholder="Ejemplo: Desarrollo Web I">

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
                <input type="text" name="precio" id="precio" placeholder="Q.0.00">

                <input type="submit" value="Agregar curso" class="btn_save">
            </form>

        </div>
    

	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>