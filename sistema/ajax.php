<?php

use LDAP\Result;

session_start();
include "../conexion.php"; 

    //Extraer datos del curso
    if(!empty($_POST)){

        if($_POST['action']=='infoCurso'){

            //print_r($_POST);

            $codigo_cur = $_POST['curso'];

            $query = mysqli_query($conection, "SELECT c.codigo_cur, c.descripcion, f.facultad, h.horario, h.idhorario, c.precio 
                                               FROM curso c
                                               INNER JOIN facultad f ON c.facultad = f.idfacultad
                                               INNER JOIN horario h ON c.horario = h.idhorario
                                               WHERE codigo_cur = '$codigo_cur' 
                                               AND estado = 1");

            mysqli_close($conection);

            $result = mysqli_num_rows($query);

            if($result > 0){
                $data = mysqli_fetch_assoc($query);  //extraye info del query que se ejecuta y guarda en data
                echo json_encode($data, JSON_UNESCAPED_UNICODE); //funcion jason devuelve tildes si es que hay y no de signos raros
                exit;
            }
            echo 'error';
            exit;
            
        }
        
    }
    

    if($_POST['action']=='buscar_alumno'){

        if(!empty($_POST['alumno'])){

            $carnet = $_POST['alumno'];

            $query = mysqli_query($conection,"SELECT * FROM alumno WHERE carnet LIKE '$carnet' AND estado = 1");

            mysqli_close($conection);
            
            $result = mysqli_num_rows($query);

            $data = '';

            if($result > 0){
                $data = mysqli_fetch_assoc($query);  //extraye info del query que se ejecuta y guarda en data
            }
            else{
                $data=0;
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE); //funcion jason devuelve tildes si es que hay y no de signos raros
        }
        exit;
    }

    //registro alumno desde matricula
    if($_POST['action']=='addAlumno'){

        $carnet     = $_POST['carnet'];
        $nombre     = $_POST['nombre_alumno'];
        $apellido   = $_POST['apellido_alumno'];
        $direccion  = $_POST['direccion_alumno'];
        $dpi        = $_POST['dpi'];
        $telefono   = $_POST['telefono_alumno'];
        $idusuario  = $_SESSION['idusuario'];

        $query_insert=mysqli_query($conection, "INSERT INTO alumno(nombre,apellido,direccion,dpi,telefono,idusuario)
                                                    VALUES('$nombre','$apellido','$direccion','$dpi','$telefono','$idusuario')");

        if($query_insert){
            $aux_carnet = mysqli_insert_id($conection);
            $msg = $aux_carnet;
        }                    
        else{
            $msg='error';
        }
        mysqli_close($conection);
        echo $msg;
        exit;
    }

    //agregando cursos al detalle de la inscripcion
    if($_POST['action']=='addCursoDetalle'){
        
        //print_r($_POST); exit;
        if(empty($_POST['curso']) || empty($_POST['horario'])){
            echo 'error';
        }
        else{
            $codcurso = $_POST['curso'];
            $horario = $_POST['horario'];
            $token = md5($_SESSION['idusuario']);

            $query_detalle = mysqli_query($conection, "CALL add_detalle ($codcurso,'$token',$horario)");
            $result = mysqli_num_rows($query_detalle);

            $detalleTabla = '';
            $total = 0;
            $arrayData = array();

            if($result > 0){
                
                while($data = mysqli_fetch_assoc($query_detalle)){
                    $precioTotal = round($data['colegiatura'], 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '
                        <tr>
                            <td>'.$data['codigo_cur'].'</td>
                            <td colspan="2">'.$data['descripcion'].'</td>
                            <td class="textcenter">'.$data['facultad'].'</td>
                            <td class="textcenter">'.$data['horario'].'</td>
                            <td class="textright">'.$precioTotal.'</td>
                            <td class=""><a href="#" class="link_delete" onclick="event.preventDefault(); eliminar_detalle('.$data['correlativo_temp'].');"><i class="fas fa-cat"></i>Miau</a></td>
                        </tr>
                    ';
                    
                }
                $total = round($total,2);

                $detalleTotal ='
                    <tr>
                        <td colspan="5" class="textright">TOTAL Q.</td>
                        <td class="textright">'.$total.'</td>
                    </tr>
                ';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['total'] = $detalleTotal;

                echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);

            }else{
                echo 'error';
            }
            mysqli_close($conection);
        }
        exit;
    }

    //extraer datos del detalle temp
    //esta funcion sirve para que al refrescar la pantalla se mantenga la informacion visible en el navegadior
    if($_POST['action']=='searchDetalle'){
        
        if(empty($_POST['user'])){
            echo 'error';
        }
        else{

            $token = md5($_SESSION['idusuario']);

            $query = mysqli_query($conection,"SELECT t.correlativo_temp, t.codigo_cur, c.descripcion,f.facultad,h.horario,t.colegiatura
                                              FROM detalle_matricula t
                                              INNER JOIN curso c ON t.codigo_cur = c.codigo_cur
                                              INNER JOIN horario h  ON t.horario = h.idhorario
                                              INNER JOIN facultad f ON f.idfacultad = c.facultad
                                              WHERE token_user = '$token'"); 

            $result = mysqli_num_rows($query);

            $detalleTabla = '';
            $total = 0;
            $arrayData = array();

            if($result > 0){
                
                while($data = mysqli_fetch_assoc($query)){
                    $precioTotal = round($data['colegiatura'], 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '
                        <tr>
                            <td>'.$data['codigo_cur'].'</td>
                            <td colspan="2">'.$data['descripcion'].'</td>
                            <td class="textcenter">'.$data['facultad'].'</td>
                            <td class="textcenter">'.$data['horario'].'</td>
                            <td class="textright">'.$precioTotal.'</td>
                            <td class=""><a href="#" class="link_delete" onclick="event.preventDefault(); eliminar_detalle('.$data['correlativo_temp'].');"><i class="fas fa-cat"></i>Miau</a></td>
                        </tr>
                    ';
                }
                $total = round($total,2);

                $detalleTotal ='
                    <tr>
                        <td colspan="5" class="textright">TOTAL Q.</td>
                        <td class="textright">'.$total.'</td>
                    </tr>
                ';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['total'] = $detalleTotal;

                echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);

            }else{
                echo 'error';
            }
            mysqli_close($conection);
        }
        exit;
    }

    //eliminar detalles
    if($_POST['action']=='eliminar_detalle'){

        if(empty($_POST['id_detalle'])){
            echo 'error';
        }
        else{
            $id_detalle = $_POST['id_detalle'];
            $token      = md5($_SESSION['idusuario']);

            $query_detalle = mysqli_query($conection, "CALL eliminar_detalle ($id_detalle,'$token')");
            $result = mysqli_num_rows($query_detalle);

            $detalleTabla = '';
            $total = 0;
            $arrayData = array();

            if($result > 0){
                
                while($data = mysqli_fetch_assoc($query_detalle)){
                    $precioTotal = round($data['colegiatura'], 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '
                        <tr>
                            <td>'.$data['codigo_cur'].'</td>
                            <td colspan="2">'.$data['descripcion'].'</td>
                            <td class="textcenter">'.$data['facultad'].'</td>
                            <td class="textcenter">'.$data['horario'].'</td>
                            <td class="textright">'.$precioTotal.'</td>
                            <td class=""><a href="#" class="link_delete" onclick="event.preventDefault(); eliminar_detalle('.$data['correlativo_temp'].');"><i class="fas fa-cat"></i>Miau</a></td>
                        </tr>
                    ';
                }
                $total = round($total,2);

                $detalleTotal ='
                    <tr>
                        <td colspan="5" class="textright">TOTAL Q.</td>
                        <td class="textright">'.$total.'</td>
                    </tr>
                ';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['total'] = $detalleTotal;

                echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);

            }else{
                echo 'error';
            }
            mysqli_close($conection);
        } 
        exit;
    }

    //Anular inscripcion
    if($_POST['action']=='anularInscripcion'){

        $token = md5($_SESSION['idusuario']);

        $query =  mysqli_query($conection, "DELETE FROM detalle_matricula WHERE token_user = '$token'");
        mysqli_close($conection);

        if($query){
            echo 'Ok';
        }
        else{
            echo 'No se puedo eliminar detalle';
        }
        exit;
    }

    //procesar inscripcion
    if($_POST['action']=='procesarInscripcion'){
        //print_r($_POST);exit;

        $carnet = $_POST['carnet'];
        $token  = md5($_SESSION['idusuario']);
        $usuario =$_SESSION['idusuario'];

        $query = mysqli_query($conection,"SELECT * FROM detalle_matricula WHERE token_user = '$token'");
        $result = mysqli_num_rows($query);

        if($result > 0){

            $query_procesar = mysqli_query($conection, "CALL procesar_inscripcion($usuario, $carnet,'$token')");
            $result_procesar = mysqli_num_rows($query_procesar);

            if($result_procesar > 0){
                $data = mysqli_fetch_assoc($query_procesar);

                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            }else{
                echo "error";
            }
        }else{
            echo "error";
        }
        mysqli_close($conection);
        exit;
 
    }

    //info Boleta para anular
    if($_POST['action']=='infoBoleta'){
        
        if(!empty($_POST['noboleta'])){

            $noboleta = $_POST['noboleta'];
            $query = mysqli_query($conection,"SELECT * FROM boleta WHERE noboleta = '$noboleta' AND estado = 1");
            mysqli_close($conection);

            $result = mysqli_num_rows($query);

            if($result > 0){

                    $data = mysqli_fetch_assoc($query);
    
                    echo json_encode($data, JSON_UNESCAPED_UNICODE);
                    exit;

            }
        }
        echo "error";
        exit;
    }

    //Proceso de anulacion de boleta
    if($_POST['action']=='anularBoleta'){
        
        if(!empty($_POST['noboleta'])){

            $noboleta = $_POST['noboleta'];
            $query = mysqli_query($conection,"CALL anular_boleta($noboleta)");
            mysqli_close($conection);

            $result = mysqli_num_rows($query);

            if($result > 0){

                    $data = mysqli_fetch_assoc($query);
    
                    echo json_encode($data, JSON_UNESCAPED_UNICODE);
                    exit;

            }
        }
        echo "error";
        exit;
    }

    //cambio de clave de usuario
    if($_POST['action']=='cambiarClave'){
        
        //print_r($_POST);

        if(!empty($_POST['passActual']) && !empty($_POST['passNuevo'])){

            $password = md5($_POST['passActual']);
            $newPass = md5($_POST['passNuevo']);
            $idUser = $_SESSION['idusuario'];
            $code = '';
            $msg = '';
            $arrayData = array();

            $query_user = mysqli_query($conection, "SELECT * FROM usuario
                                                    WHERE clave = '$password' AND idusuario = $idUser");

            $result = mysqli_num_rows($query_user);

            if($result > 0){
                
                $query_update = mysqli_query($conection, "UPDATE usuario SET clave = '$newPass' WHERE idusuario = $idUser");
                mysqli_close($conection);

                if($query_update){
                    $code = '00';
                    $msg = "Su clave ha sido actualizada con exito.";
                }else{
                    $code = '2';
                    $msg = "No se pudo actualizar la clave.";
                }
                
            }else{
                $code = '1';
                $msg = "La clave actual es incorrecta.";
            }

            $arrayData = array('cod' => $code, 'msg' => $msg);
            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);

        }else{
            echo "error";
        }

        exit;
    }

    //actualizacion de datos universidad
    if($_POST['action']=='actualizarUniversidad'){

        if(empty($_POST['txt_nombre']) || empty($_POST['txt_tel']) || empty($_POST['txt_direccion']) || empty($_POST['txt_correo']) ){

            $code = '1';
            $msg = "Todos los campos son obligatorios";
        }else{
            $nombre = $_POST['txt_nombre'];
            $telefono = intval($_POST['txt_tel']);
            $correo = $_POST['txt_correo'];
            $direccion = $_POST['txt_direccion'];

            $update_u = mysqli_query($conection, "UPDATE configuracion SET
                                                  nombre = '$nombre',
                                                  telefono = '$telefono',
                                                  correo ='$correo',
                                                  direccion = '$direccion'");

            mysqli_close($conection);

            if($update_u){
                $code = '00';
                $msg = "Se han actualizado los datos con exito";
            }else{
                $code = '2';
                $msg = "Error al actualizar los datos";
            }

        }
        $arrayData = array('cod'=> $code, 'msg'=>$msg);
        echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
?>