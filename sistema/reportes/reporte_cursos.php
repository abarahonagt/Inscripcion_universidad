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

		.logo {
			max-width: 50%;
			max-height: 50%;
		}

		.reporte {
			border-collapse: collapse;
			align-items: center;
			align-content: center;
			margin: auto;

		}

		.reporte th {
			background: #2E86C1;
			color: #FFF;
			padding: 5px;
			font-size: 9pt;
		}

		.reporte tr {
			font-size: 7pt;
			text-align: center;
		}

		.reporte tr:nth-child(even) {
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
					<h1 class="titulo">Reporte de Cursos activos en la plataforma</h1><br>
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
				Descripcion
			</th>
			<th width="120px" class="textcenter">
				Facultad
			</th>
			<th width="190px" class="textcenter">
				Catedratico
			</th>
			<th width="120px" class="textcenter">
				Horario
			</th>
			<th class="textcenter">
				Precio
			</th>
		</tr>

		<?php

		$query = mysqli_query($conection, "SELECT cu.codigo_cur, cu.descripcion, f.facultad, ca.nombre, ca.apellido, h.horario, cu.precio
											FROM curso cu 
											INNER JOIN facultad f ON cu.facultad = f.idfacultad
											INNER JOIN catedratico ca ON cu.catedratico = ca.codigo_cat
											INNER JOIN horario h ON cu.horario = h.idhorario
											WHERE cu.estado = 1 
											ORDER BY codigo_cur ASC");


		$result = mysqli_num_rows($query);

		if ($result > 0) {

			while ($data = mysqli_fetch_array($query)) {

		?>

				<tr>
					<td>
						<?php echo $data["codigo_cur"] ?>
					</td>
					<td>
						<?php echo $data["descripcion"] ?>
					</td>
					<td>
						<?php echo $data["facultad"] ?>
					</td>
					<td>
						<?php echo $data["nombre"] ?> <?php echo $data["apellido"] ?>
					</td>
					<td>
						<?php echo $data["horario"] ?>
					</td>
					<td>
						Q. <?php echo $data["precio"] ?>
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

$dompdf->stream('Reporte_cursos.pdf', array("Attachment" => false));




?>