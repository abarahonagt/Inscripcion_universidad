<?php

session_start();
ob_start();

if (empty($_SESSION['active'])) {
	header('location: ../');
}

include "../../conexion.php";

$imagen = "logo_color.jpg";
$imagenB64 = "data:image/jpg;base64," . base64_encode(file_get_contents($imagen));


?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Reporte de alumnos</title>

	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		p,
		label,
		span,
		table {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 9pt;
		}

		.titulo {
			text-align: center;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 18pt;
			color: #21618C;
		}

		.logo{
			max-width: 50%;
			max-height: 50%;
		}

		.reporte{
			border-collapse: collapse;
			align-items: center;
			align-content: center;
			padding: 48px;
			
		}
		.reporte th{
			background: #2E86C1;
			color: #FFF;
			padding: 5px;
			font-size: 9pt;
		}

		.reporte tr{
			font-size: 7pt;
			text-align: center;
		}

		.reporte tr:nth-child(even){
			background: #ededed;
		}

		.textcenter {
			text-align: center;
		}

	</style>
</head>

<body>
	<table class="cabecera">
		<tr>
			<td>
				<div>
					<img src="<?php echo $imagenB64; ?>">
				</div>
			</td>
			<td>
				<div>
					<h1 class="titulo">Reporte de alumnos activos en la plataforma</h1><br>
				</div>
			</td>
		</tr>
	
	</table>

	<table class="reporte">
		<tr>
			<th class="textcenter">
				Carnet
			</th>
			<th width="120px" class="textcenter">
				Nombre
			</th>
			<th width="120px" class="textcenter">
				Apellido
			</th>
			<th width="190px" class="textcenter">
				Direccion
			</th>
			<th width="120px" class="textcenter">
				DPI
			</th>
			<th class="textcenter">
				Telefono
			</th>
		</tr>

		<?php

		$query = mysqli_query($conection, "SELECT *
										FROM alumno  
										WHERE estado = 1
										ORDER BY carnet ASC");


		$result = mysqli_num_rows($query);

		if ($result > 0) {

			while ($data = mysqli_fetch_array($query)) {

		?>

				<tr>
					<td>
						<?php echo $data["carnet"]; ?>
					</td>
					<td>
						<?php echo $data["nombre"]; ?>
					</td>
					<td>
						<?php echo $data["apellido"]; ?>
					</td>
					<td>
						<?php echo $data["direccion"]; ?>
					</td>
					<td>
						<?php echo $data["dpi"]; ?>
					</td>
					<td>
						<?php echo $data["telefono"]; ?>
					</td>
				</tr>

		<?php
			} //while
		} //if
		?>
	</table>

</body>

<?php



//echo $html;


require_once '../pdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use LDAP\Result;


$options = new Options();
$options->set('isRemoteEnable', TRUE);

$dompdf = new Dompdf($options);

$html = ob_get_clean();


$dompdf->loadHtml("$html");

//$dompdf -> setPaper('letter');
$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream('Reporte_Alumnos.pdf', array("Attachment" => false));




?>