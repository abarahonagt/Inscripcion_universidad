<?php

    session_start();
    include "../conexion.php";

    //echo md5($_SESSION['idusuario']);
    
?>

<!DOCTYPE html>
<html lang="es">
 
<head>
    <meta charset="UTF-8"> 
    <?php include "includes/scripts.php"; ?>
    <title>Nueva Inscripcion</title>
</head>
<body>
    <?php 
        include "includes/header.php"; 
    ?>
     
<!-- -----------------------------------ESTRUCTURA DE LA PAGINA INICIA AQUI--------------------------->

    <section id="container">
            <div class="title_page">
                <h1><i class="fas fa-school"></i> Nueva inscripción</h1>
            </div>

            <div class="datos_alumno">
                <div class="action_alumno">
                    <h4> Datos del alumno</h4>
                    <?php
					    if($_SESSION['rol'] == 1){
				    ?>
                    <a href="#" class="btn_new btn_new_alumno"><i class="fas fa-plus"></i> Nuevo Alumno</a>
                    <?php
                        }
                    ?>        
                    
                </div>
<!--        </div>
    </section> -->
                <form name="form_new_alumno" id="form_new_alumno" class="datos">
                    
                <input type="hidden" name="action" value="addAlumno">
                <input type="hidden" id="carnet_h" name="carnet_h" value="required">

                    <div class="wd30">
                        <label>Carnet</label>
                        <input type="text" name="carnet" id="carnet" placeholder="Ingrese carnet para buscar" disable required>
                    </div>
                
                    <div class="wd30">
                        <label>Nombre</label>
                        <input type="text" name="nombre_alumno" id="nombre_alumno" disable required>
                    </div>

                    <div class="wd30">
                        <label>Apellido</label>
                        <input type="text" name="apellido_alumno" id="apellido_alumno" disable required>
                    </div>


                    <div class="wd30">
                        <label>DPI</label>
                        <input type="text" name="dpi" id="dpi" disable required>
                    </div>

                    <div class="wd30">
                        <label>Telefono</label>
                        <input type="text" name="telefono_alumno" id="telefono_alumno" disable required>
                    </div>

                    <div class="wd100">
                        <label>Direccion</label>
                        <input type="text" name="direccion_alumno" id="direccion_alumno" disable required>
                    </div>

                    <div id="div_registro_alumno" class="wd100">
                        <button type="submit" class="btn_save" aling="right"><i class="fas fa-save"></i>Guardar</button>
                    </div>

                </form>
            </div>

            <div class="datos_inscripcion">
                <h4>Datos de inscripcion</h4>
                <div class="datos">
                    <div class="wd50">
                        <label>Usuario</label>
                        <p><?php echo $_SESSION['nombre'];?> <?php echo $_SESSION['apellido'];?></p>
                    </div>
                    <div class="wd50">
                        <p>Acciones</p>
                        <a href="#" class="btn_cancel textcenter" id="btn_anular"><i class="fas fa-trash"></i> Anular</a>
                        <a href="#" class="btn_ok textcenter" id="btn_procesar" ><i class="fas fa-check"></i> Procesar</a> 
                    </div>
                </div>
            </div>

            <table class="tb_inscripcion">
                <thead>
                    <tr>
                        <th width="150px">Codigo del curso</th>
                        <th colspan="2">Descripcion</th>
                        <th>Facultad</th>
                        <th>Horario</th>
                        <th class="textright">Colegiatura</th>
                        <th>Acciones</th>
                    </tr>

                    <tr>
                        <td><input type="text" name="txt_codigo" id="txt_codigo"></td> 
                        <input type="hidden" id="horario_h" name="horario_h" value="required">
                        <td id="txt_descripcion" colspan="2">-</td>
                        <td id="txt_facultad">-</td>
                        <td><input type="text" name="txt_horario" id="txt_horario" disabled></td>
                        <!--<td id="txt_horario">-</td>-->
                        <td id="txt_colegiatura" class="textright">0.00</td>
                        <td><a href="#" class="add_curso" id="add_curso">Agregar</a>
                    </tr>  
                    
                    <tr>
                        <th>Codigo del curso</th>
                        <th colspan="2">Descripcion</th>
                        <th>Facultad</th>
                        <th>Horario</th>
                        <th class="textright">Colegiatura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                
                <tbody id="detalle_inscripcion">
                        <!--CONTENIDO LLENO POR MEDIO DE AJAX-->

                </tbody>

                <tfoot id="detalle_totales">
                        <!--CONTENIDO LLENO POR MEDIO DE AJAX-->
                </tfoot> 
            </table>
    </section>

    <?php 
        include "includes/footer.php";
    ?>

<!--esta funcion sirve para que al refrescar la pantalla se mantenga la informacion visible en el navegadior-->
    <script type ="text/javascript">
        $(document).ready(function(){
            var iduser = '<?php echo $_SESSION['idusuario'];?>'
            searchDetalle(iduser);
        
        });
    </script>

</body>
</html>