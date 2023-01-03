


<?php

    $host = 'localhost';
    $user = 'root';
    $password = '';
    $db = 'universidad';

    $conection = @mysqli_connect($host,$user,$password,$db);

        if(!$conection){
            echo "Ocurrio un error durante la conexion con la db.";
        }
?>

