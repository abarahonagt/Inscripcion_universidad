<?php

session_start();
include "../conexion.php";

/* $busqueda='';

if(!empty($_REQUEST['busqueda'])){
    if(!is_numeric($_REQUEST['busqueda'])){
        header("location: lista_matricula.php");
    }
    $busqueda=strtolower($_REQUEST['busqueda']);
    $where = "noboleta = $busqueda";
    $buscar = "busqueda=$busqueda";
} */

$busqueda = strtolower($_REQUEST['busqueda']);

if (empty($busqueda)) {
    header("location: lista_matricula.php");
}else{
    $busqueda=strtolower($_REQUEST['busqueda']);
    $where = "noboleta = $busqueda";
    $buscar = "busqueda=$busqueda";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Listado de matriculas realizadas</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<h1>Listado de matriculas realizadas</h1>
        <a href="nueva_inscripcion.php" class="btn_new"><i class="fas fa-plus"></i> Nueva inscripcion</a>

        <!--buscador en lista-->
        
        <form action="buscar_matricula.php" method="get" class="form_search">
        <input type ="text" name="busqueda" id="busqueda" placeholder="No. Boleta" value="<?php echo $busqueda; ?>">
            <input type="submit" value="Buscar" class="btn_search">
        </form>



            <table>
                <tr>
                    <th>
                        No.
                    </th>
                    <th>
                        Fecha
                    </th>
                    <th>
                        Nombre del Alumno
                    </th>
                    <th>
                        Apellido del Alumno
                    </th>
                    <th>
                        Atendio
                    </th>
                    <th>
                        Estado
                    </th>
                    <th class="textright">
                        Total
                    </th>
                    <th class="textright">
                        Acciones
                    </th>
                </tr>


                <?php
                    
                    //configuracion paginador en PHP

                    $query_register = mysqli_query($conection, "SELECT COUNT(*) AS total_register FROM boleta WHERE $where ");
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


                    $query = mysqli_query($conection, "SELECT b.noboleta, b.fecha, b.total, b.carnet, b.estado, 
                                                       u.nombre AS vendedor,
                                                       al.nombre AS nombreAlumno,
                                                       al.apellido AS apellidoAlumno
                                                       FROM boleta b
                                                       INNER JOIN usuario u ON b.usuario = u.idusuario
                                                       INNER JOIN alumno al ON b.carnet = al.carnet
                                                       WHERE $where AND b.estado != 3
                                                       ORDER BY b.fecha DESC LIMIT $desde,$por_pagina"); 
                     
                //

                    $result = mysqli_num_rows($query);

                    if($result>0){

                        while($data = mysqli_fetch_array($query)){

                            if($data["estado"]==1){
                                $estado = '<span class="matriculado">Matriculado</span>';
                            }else{
                                $estado = '<span class="anulado">Anulado</span>';
                            }
                         
                ?>
                            <tr id="row_<?php echo $data ["noboleta"]; ?>">
                                <td>
                                    <?php echo $data["noboleta"] ?>
                                </td>
                                <td>
                                    <?php echo $data["fecha"] ?>
                                </td>
                                <td>
                                    <?php echo $data["nombreAlumno"] ?>
                                </td>
                                <td>
                                    <?php echo $data["apellidoAlumno"] ?>
                                </td>
                                <td>
                                    <?php echo $data["vendedor"] ?>
                                </td>
                                <td class="estado">
                                    <?php echo $estado; ?>
                                </td>
                                <td class="textright totalBoleta"><span>Q.</span>
                                    <?php echo $data["total"] ?>
                                </td>
                                <td>
                                    <div class="div_acciones">
                                        <div>
                                            <button class="btn_view view_boleta" type="button" al="<?php echo $data["carnet"]?>" b="<?php echo $data['noboleta']; ?>"><i class="fas fa-eye"></i></button>
                                        </div>
                                    
                                    <?php

                                        if($data["estado"]==1){

                                    ?>
                                    <div class="div_boletas">
                                        <button class="btn_anular anular_boleta" type="button" b="<?php echo $data["noboleta"]?>"><i class="fas fa-ban"></i></button>
                                    </div>
                                    
                                    <?php
                                        }else{
                                    ?>

                                    <div class="div_boletas">
                                        <button class="btn_anular anular_boleta inactive" b="<?php echo $data["noboleta"]?>"><i class="fas fa-ban"></i></button>
                                    </div>

                                    <?php
                                        }
                                    ?>
                                    </div>
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
                    <li><a href="?pagina=<?php echo 1;?>&<?php echo $buscar; ?>">|<</a></li>
                    <li><a href="?pagina=<?php echo $pagina-1;?>&<?php echo $buscar; ?>"><<</a></li>
                        
                        <?php 
                        }
                            for($i=1; $i <= $total_paginas; $i++){

                                if($i == $pagina){
                                    echo '<li class= "pageSelected">'.$i.'</li>';
                                }
                                else{
                                    echo '<li><a href="?pagina='.$i.'">'.$i.'&'.$buscar.'</a></li>';
                                }
                            }
                            if($pagina != $total_paginas){

                        ?>
                
                    <li><a href="?pagina=<?php echo $pagina+1;?>&<?php echo $buscar; ?>">>></a></li>
                    <li><a href="?pagina=<?php echo $total_paginas;?>&<?php echo $buscar; ?>">>|</a></li>
                    <?php } ?> 
                </ul>
            </div>


	</section>
</body>
<?php include "includes/footer.php"; ?>
</html>