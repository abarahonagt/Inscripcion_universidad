<?php

    //validacion de acceso por tipo de rol 1= admin 2=estudiante 3=catedratico
session_start();

if($_SESSION['rol'] != 1){
    header("location:./"); 
}
    include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
		<?php include "includes/scripts.php"; ?>
	<title>Listado de usuarios</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<h1>Listado de usuarios</h1>
        <a href="registro_usuario.php" class="btn_new">Crear usuario</a>

        <!--buscador en lista-->
        
        <form action="buscar_usuario.php" method="get" class="form_search">
            <input type ="text" name="busqueda" id="busqueda" placeholder="Buscar">
            <input type="submit" value="Buscar" class="btn_search">
        </form>



            <table>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Nombre
                    </th>
                    <th>
                        Apellido
                    </th>
                    <th>
                        Correo
                    </th>
                    <th>
                        Usuario
                    </th>
                    <th>
                        Rol
                    </th>
                    <th>
                        Acciones
                    </th>
                </tr>


                <?php
                    
                    //configuracion paginador en PHP

                    $query_register = mysqli_query($conection, "SELECT COUNT(*) AS total_register FROM usuario WHERE estado = 1");
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


                    $query = mysqli_query($conection, "SELECT u.idusuario, u.nombre, u.apellido, u.correo, u.usuario, r.rol
                                                       FROM usuario u INNER JOIN rol r ON u.rol = r.idrol 
                                                       WHERE estado = 1
                                                       ORDER BY u.idusuario ASC
                                                       LIMIT $desde,$por_pagina");  
                //

                    $result = mysqli_num_rows($query);

                    if($result>0){

                        while($data = mysqli_fetch_array($query)){
                         
                ?>
                            <tr>
                                <td>
                                    <?php echo $data["idusuario"] ?>
                                </td>
                                <td>
                                    <?php echo $data["nombre"] ?>
                                </td>
                                <td>
                                    <?php echo $data["apellido"] ?>
                                </td>
                                <td>
                                    <?php echo $data["correo"] ?>
                                </td>
                                <td>
                                    <?php echo $data["usuario"] ?>
                                </td>
                                <td>
                                    <?php echo $data["rol"] ?>
                                </td>
                                <td>
                                    <!-- despues del ID=? eso indica envio de datos por medio de URL -->
                                    <a class="link_edit" href="editar_usuario.php?id=<?php echo $data["idusuario"]; ?>" ><i class="fas fa-edit"></i> Editar</a>
                                       
                                    <?php if($data["idusuario"] != 1){ ?>
                                         | 
                                        <a class="link_delete" href="eliminar_usuario.php?id=<?php echo $data["idusuario"]; ?>" ><i class="fas fa-trash"></i> Eliminar</a>
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