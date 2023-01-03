<?php

session_start();
include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Listado de Cursos</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">

    <?php

        $busqueda = strtolower($_REQUEST['busqueda']);

        if (empty($busqueda)) {
            header("location: lista_curso.php");
        }

    ?>


		<h1>Listado de Cursos</h1>
        <?php if($_SESSION["rol"] == 1){ ?>
        <a href="registro_curso.php" class="btn_new">Crear Curso</a>
        <?php } ?>

        <!--buscador en lista-->
        
        <form action="buscar_curso.php" method="get" class="form_search">
            <input type ="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda ?>">
            <input type="submit" value="Buscar" class="btn_search">
        </form>



            <table>
                <tr>
                    <th>
                        Codigo
                    </th>
                    <th>
                        Descripcion
                    </th>
                    <th>
                        Facultad
                    </th>
                    <th>
                        Catedratico
                    </th>
                    <th>
                        Horario
                    </th>
                    <th>
                        Precio
                    </th>
                    <th>
                        <?php
			                if($_SESSION['rol'] == 1){
		                ?>
                        Acciones
                        <?php } ?>
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
                                                                FROM curso cu
                                                                INNER JOIN facultad f ON cu.facultad = f.idfacultad
                                                                INNER JOIN catedratico ca ON cu.catedratico = ca.codigo_cat
                                                                INNER JOIN horario h ON cu.horario = h.idhorario
                                                                WHERE (cu.codigo_cur LIKE '%$busqueda%' OR
                                                                       cu.descripcion LIKE '%$busqueda%' OR
                                                                       f.facultad LIKE '%$busqueda%' OR
                                                                       ca.nombre LIKE '%$busqueda%' OR
                                                                       h.horario LIKE '%$busqueda%' OR
                                                                       cu.precio LIKE '%$busqueda%')
                                                                AND cu.estado = 1");

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
                                                        FROM curso cu
                                                        INNER JOIN facultad f ON cu.facultad = f.idfacultad
                                                        INNER JOIN catedratico ca ON cu.catedratico = ca.codigo_cat
                                                        INNER JOIN horario h ON cu.horario = h.idhorario
                                                        WHERE (cu.codigo_cur LIKE '%$busqueda%' OR
                                                                cu.descripcion LIKE '%$busqueda%' OR
                                                                f.facultad LIKE '%$busqueda%' OR
                                                                ca.nombre LIKE '%$busqueda%' OR
                                                                h.horario LIKE '%$busqueda%'OR
                                                                cu.precio LIKE '%$busqueda%')
                                                       AND cu.estado = 1
                                                       ORDER BY codigo_cur ASC
                                                       LIMIT $desde,$por_pagina");  
                //

                    $result = mysqli_num_rows($query);

                    if($result>0){

                        while($data = mysqli_fetch_array($query)){
                         
                ?>
                            <tr>
                                <td>
                                    <?php echo $data["codigo_cur"] ?>
                                </td>
                                <td>
                                    <?php echo $data["descripcion"] ?>
                                </td>
                                <td>
                                    <?php echo $data["facultad"] ?>
                                </td>
                                <td>
                                    <?php echo $data["catedratico"] ?>
                                </td>
                                <td>
                                    <?php echo $data["horario"] ?>
                                </td>
                                <td>
                                    <?php echo $data["precio"] ?>
                                </td>
                                <td>
                                    <?php if($_SESSION["rol"] == 1){ ?>
                                    <!-- despues del ID=? eso indica envio de datos por medio de URL -->
                                    <a class="link_edit" href="editar_curso.php?id=<?php echo $data["codigo_cur"]; ?>" >Editar</a>
                                       
                                    
                                        | 
                                        <a class="link_delete" href="eliminar_curso.php?id=<?php echo $data["codigo_cur"]; ?>" >Eliminar</a>
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