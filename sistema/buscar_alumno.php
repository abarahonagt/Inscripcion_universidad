<?php

session_start();
include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Listado de Alumnos</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">

    <?php

        $busqueda = strtolower($_REQUEST['busqueda']);

        if (empty($busqueda)) {
            header("location: lista_alumno.php");
        }

    ?>


		<h1>Listado de Alumno</h1>
        <?php if($_SESSION["rol"] == 1){ ?>
        <a href="registro_alumno.php" class="btn_new">Crear Alumno</a>
        <?php } ?>

        <!--buscador en lista-->
        
        <form action="buscar_alumno.php" method="get" class="form_search">
            <input type ="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda ?>">
            <input type="submit" value="Buscar" class="btn_search">
        </form>



            <table>
                <tr>
                    <th>
                        Carnet
                    </th>
                    <th>
                        Nombre
                    </th>
                    <th>
                        Apellido
                    </th>
                    <th>
                        Direccion
                    </th>
                    <th>
                        DPI
                    </th>
                    <th>
                        Telefono
                    </th>
                    <th>
                        Acciones
                    </th>
                </tr>


                <?php
                    
                    //configuracion paginador en PHP y busqueda

                    $rol = ' ';

                    if ($busqueda == 'administrador') {
                        $rol = " OR rol LIKE '%1%'";
                    }
                    elseif ($busqueda == 'alumno') {
                        $rol = " OR rol LIKE '%2%'";
                    }
                    elseif ($busqueda == 'catedratico') {
                        $rol = " OR rol LIKE '%3%'";
                    }

                    $query_register = mysqli_query($conection, "SELECT COUNT(*) AS total_register 
                                                                FROM alumno 
                                                                WHERE (carnet LIKE '%$busqueda%' OR
                                                                       nombre LIKE '%$busqueda%' OR
                                                                       apellido LIKE '%$busqueda%' OR
                                                                       direccion LIKE '%$busqueda%' OR
                                                                       dpi LIKE '%$busqueda%' OR
                                                                       telefono LIKE '%$busqueda%')
                                                                AND estado = 1");

                    $result_register = mysqli_fetch_array($query_register);
                    $total_register = $result_register['total_register'];

                    $por_pagina = 5;

                    if(empty($_GET['pagina'])){
                        $pagina=1;
                    }
                    else{
                        $pagina=$_GET['pagina'];
                    }

                    $desde = ($pagina-1)*$por_pagina;
                    $total_paginas = ceil($total_register/$por_pagina); 


                    $query = mysqli_query($conection, "SELECT *
                                                       FROM alumno
                                                       WHERE (carnet LIKE '%$busqueda%' OR
                                                            nombre LIKE '%$busqueda%' OR
                                                            apellido LIKE '%$busqueda%' OR
                                                            direccion LIKE '%$busqueda%' OR
                                                            dpi LIKE '%$busqueda%' OR
                                                            telefono LIKE '%$busqueda%')
  
                                                       AND estado = 1
                                                       ORDER BY carnet ASC
                                                       LIMIT $desde,$por_pagina");  
                //

                    $result = mysqli_num_rows($query);

                    if($result>0){

                        while($data = mysqli_fetch_array($query)){
                         
                ?>
                            <tr>
                                <td>
                                    <?php echo $data["carnet"] ?>
                                </td>
                                <td>
                                    <?php echo $data["nombre"] ?>
                                </td>
                                <td>
                                    <?php echo $data["apellido"] ?>
                                </td>
                                <td>
                                    <?php echo $data["direccion"] ?>
                                </td>
                                <td>
                                    <?php echo $data["dpi"] ?>
                                </td>
                                <td>
                                    <?php echo $data["telefono"] ?>
                                </td>
                                <td>
                                    <!-- despues del ID=? eso indica envio de datos por medio de URL -->
                                    <a class="link_edit" href="editar_alumno.php?id=<?php echo $data["carnet"]; ?>" >Editar</a>
                                       
                                    <?php if($_SESSION["rol"] == 1){ ?>
                                        | 
                                        <a class="link_delete" href="eliminar_alumno.php?id=<?php echo $data["carnet"]; ?>" >Eliminar</a>
                                    <?php } ?>
                                </td>
                            </tr>
                <?php
                        }
                    }

                ?>


                
            </table>
            <!--validacion: si encuentra el objetro, mostrara el paginador, sino no muestra el paginador-->
            <?php
                if($total_register != 0){
            ?>


            <!--INICIA CONFIGURACION DEL PAGINADOR-->
            <div class="paginador">
                <ul>
                <?php
                    if($pagina != 1){
                ?>                
                    <li><a href="?pagina=<?php echo 1;?> &busqueda=<?php echo $busqueda; ?>">|<</a></li>
                    <li><a href="?pagina=<?php echo $pagina-1;?>&busqueda=<?php echo $busqueda; ?>"><<</a></li>
                        
                        <?php 
                        }
                            for($i=1; $i <= $total_paginas; $i++){

                                if($i == $pagina){
                                    echo '<li class= "pageSelected">'.$i.'</li>';
                                }
                                else{
                                echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
                                }
                            }
                            if($pagina != $total_paginas){

                        ?>
                
                    <li><a href="?pagina=<?php echo $pagina+1;?>&busqueda=<?php echo $busqueda; ?>">>></a></li>
                    <li><a href="?pagina=<?php echo $total_paginas;?>&busqueda=<?php echo $busqueda; ?>">>|</a></li>
                    <?php } ?> 
                </ul>
            </div>
            <?php } ?>                        

	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>