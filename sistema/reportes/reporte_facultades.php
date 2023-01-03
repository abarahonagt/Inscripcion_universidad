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
	<title>Reporte de Cursos</title>

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
			margin: auto;
			
		}
		.reporte th{
			background: #2E86C1;
			color: #FFF;
			padding: 5px;
			font-size: 12pt;
		}

		.reporte tr{
			font-size: 12pt;
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
					<h1 class="titulo">Reporte de facultades de la Universidad</h1><br>
				</div>
			</td>
		</tr>
	
	</table>

	<table class="reporte">
		<tr>
			<th class="textcenter">
				Codigo
			</th>
			<th width="120px" class="textcenter">
				Facultad
			</th>
		</tr>

		<?php

		$query = mysqli_query($conection, "SELECT *
										FROM facultad 
										ORDER BY idfacultad ASC");


		$result = mysqli_num_rows($query);

		if ($result > 0) {

			while ($data = mysqli_fetch_array($query)) {

		?>

				<tr>
					<td>
						<?php echo $data["idfacultad"]; ?>
					</td>
					<td>
						<?php echo $data["facultad"]; ?>
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

$dompdf->stream('Reporte_facultades.pdf', array("Attachment" => false));




?>