<?php

  $alert ='';
  
  //iniaciamos sesion
  session_start();

if(!empty($_SESSION['active'])){
    //cuando el inicio de sesion es valido lo redirige a esta pagina
    header('location: sistema/');
}
else{
  //acciones para el boton ingresar
  if(!empty($_POST)){
    
    if(empty($_POST['usuario']) ||  empty($_POST['clave'])){
        $alert = 'Ingrese su usuario y clave';
    }
    else{
        require_once "conexion.php";

        //variables donde se guardaran los datos obtenidos de los inputs
        //con mysqli_real... evitamos cualquier tipo de inyeccion sql 
        //con md5 encriptamos la contrasena
        $user = mysqli_real_escape_string($conection,$_POST['usuario']);
        $pass = md5(mysqli_real_escape_string($conection,$_POST['clave']));

        //consulta SQL la cual devolvera un resultado, cantidad de columnas, la cual si es mayor a 0 nos permitira recorrerla
        //y buscar los campos dentro de la tabla guardandola en result.

        $query = mysqli_query($conection,"SELECT u.idusuario, u.nombre, u.apellido, u.correo, u.usuario, r.rol, r.idrol 
                                          FROM usuario u
                                          INNER JOIN rol r ON u.rol = r.idrol 
                                          WHERE u.usuario = '$user' AND u.clave = '$pass'");
        //mysqli_close($conection);
        $result = mysqli_num_rows($query);

        if($result > 0){
            
            //array que guardara los campos de la consulta
            $data = mysqli_fetch_array($query);

            $_SESSION['active']=true;
            $_SESSION['idusuario']=$data['idusuario'];
            $_SESSION['nombre']=$data['nombre'];
            $_SESSION['apellido']=$data['apellido'];
            $_SESSION['correo']=$data['correo'];
            $_SESSION['usuario']=$data['usuario'];
            $_SESSION['clave']=$data['clave'];
            $_SESSION['rol']=$data['idrol'];
            $_SESSION['rol_nombre']=$data['rol'];

            //cuando el inicio de sesion es valido lo redirige a esta pagina
            header('location: sistema/');
        }
        else{
            $alert = 'El usuario o clave ingresado son incorrectos';
            session_destroy();
        }
    }
  }
}
?>


<!DOCTYPE html>
<html lang = "es">

<head>
    <meta charset = "UTF-8">
    <title>
        Ingresar | Charver University
    </title>
    <link rel="stylesheet" type="text/css" href= "css/style.css">
    <link rel="icon" href="img/candado.png" type="image/png">
    
</head>

<body background="img/fondo7.png" class="fondo" id="fondo">
    <section id="container">
        <form action="" method="post">
            <h3>Iniciar Sesion</h3>
            <img src = "img/candado.png" alt="Iniciar">

            <input type="text" name ="usuario" placeholder="Usuario">
            <input type="password" name ="clave" placeholder="Contraseña">
            
            <!--imprimira, segun el caso lo que se guarde en la variable alert-->
            <div class="alert"> <?php echo isset($alert)? $alert:''; ?> </div>

            <input type="submit" value ="INGRESAR">
        </form>
    </section>
</body>
</html>