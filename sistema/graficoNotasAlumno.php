<?php

//validacion de acceso por tipo de rol 1= admin 2=estudiante 3=catedratico
session_start();

if ($_SESSION['rol'] != 1) {
  header("location:./");
}
include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <?php include "includes/scripts.php"; ?>
  <title>Cursos Actuales</title>
</head>

<body>
  <?php include "includes/header.php"; ?>
  <section id="container">
    <h1>Cursos actuales</h1>


    <?php $usuario    = $_SESSION['usuario']; 
    
    $con = new mysqli('localhost', 'root', '', 'universidad');
    $query = $con->query("SELECT * FROM detalle d, boleta b, alumno a, curso c, usuario u where d.noboleta = b.noboleta 
                          AND b.carnet = a.carnet and d.codigo_cur = c.codigo_cur and a.nombre = u.nombre 
                          and a.apellido  = u.apellido and u.usuario = '$usuario'");

    foreach ($query as $data) {
      $month[] = $data['descripcion'];
      $amount[] = $data['nota'];
    }

    ?>


    <div style="width: 100px;">
      <canvas id="myChart"></canvas>
    </div>

    <script>
      // === include 'setup' then 'config' above ===
      const labels = <?php echo json_encode($month) ?>;
      const data = {
        labels: labels,
        datasets: [{
          label: 'NOTAS POR CURSO',
          data: <?php echo json_encode($amount) ?>,
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(201, 203, 207, 0.2)'
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 1
        }]
      };

      const config = {
        type: 'bar',
        data: data,
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        },
      };

      var myChart = new Chart(
        document.getElementById('myChart'),
        config
      );
    </script>



  </section>







</body>
<?php include "includes/footer.php"; ?>

</html>