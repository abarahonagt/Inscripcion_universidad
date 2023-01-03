<?php

session_start();
include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Listado de Catedraticos</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<h1>Listado de Catedraticos</h1>
        <?php
			if($_SESSION['rol'] == 1){
		?>	
        <a href="registro_catedratico.php" class="btn_new">Crear Catedratico</a>
        <a href="reportes/reporte_catedraticos.php" class="btn_new"><i class="fas fa-print"></i> Imprimir listado</a>
        <?php } ?>
        
        <!--buscador en lista-->
        
        <form action="buscar_catedratico.php" method="get" class="form_search">
            <input type ="text" name="busqueda" id="busqueda" placeholder="Buscar">
            <input type="submit" value="Buscar" class="btn_search">
        </form>



            <table>
                <tr>
                    <th>
                        Codigo
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
                        <?php
			                if($_SESSION['rol'] == 1){
		                ?>
                        Acciones
                        <?php } ?>	
                    </th>
                </tr>


                <?php
                    
                    //configuracion paginador en PHP

                    $query_register = mysqli_query($conection, "SELECT COUNT(*) AS total_register FROM catedratico WHERE estado = 1");
                    $result_register = mysqli_fetch_array($query_register);
                    $total_register = $result_register['total_register'];

                    $por_pagina = 10;

                    if(empty($_GET['pagina'])){
                        $pagina=1;
                    }
                    else{
                        $pagina=$_GET['pagina'];
                    }

                    $desde = ($pagina-1)*$por_pagina;
                    $total_paginas = ceil($total_register/$por_pagina); 


                    $query = mysqli_query($conection, "SELECT *
                                                       FROM catedratico  
                                                       WHERE estado = 1
                                                       ORDER BY codigo_cat ASC
                                                       LIMIT $desde,$por_pagina");  
                //

                    $result = mysqli_num_rows($query);

                    if($result>0){

                        while($data = mysqli_fetch_array($query)){
                         
                ?>
                            <tr>
                                <td>
                                    <?php echo $data["codigo_cat"] ?>
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
                                <?php
					                if($_SESSION['rol'] == 1){
				                ?>	
                                    <a class="link_edit" href="editar_catedratico.php?id=<?php echo $data["codigo_cat"]; ?>" ><i class="fas fa-edit"></i> Editar</a>
                                       
                                    <?php  if($_SESSION["rol"] == 1 ){ ?>
                                        | 
                                        <a class="link_delete" href="eliminar_catedratico.php?id=<?php echo $data["codigo_cat"]; ?>" ><i class="fas fa-trash"></i> Eliminar</a>
                                    <?php } ?>
                                <?php } ?>
                                </td>
                            </tr>
                <?php
                        }
                    }

                ?>


                
            </table>
            
            <!--INICIA CONFIGURACION DEL PAGINADOR-->
            <div class="paginador">
                <ul>
                    <?php
                        if($total_register != 0){
                    ?>                
                    <li><a href="?pagina=<?php echo 1;?>">|<</a></li>
                    <li><a href="?pagina=<?php echo $pagina-1;?>"><<</a></li>
                        
                        <?php 
                        }
                            for($i=1; $i <= $total_paginas; $i++){

                                if($i == $pagina){
                                    echo '<li class= "pageSelected">'.$i.'</li>';
                                }
                                else{
                                    echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
                                }
                            }
                            if($pagina != $total_paginas){

                        ?>
                
                    <li><a href="?pagina=<?php echo $pagina+1;?>">>></a></li>
                    <li><a href="?pagina=<?php echo $total_paginas;?>">>|</a></li>
                    <?php } ?> 
                </ul>
            </div>


	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>